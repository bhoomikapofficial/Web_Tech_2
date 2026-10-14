<?php
require __DIR__ . '/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$student = ['name'=>'', 'roll_number'=>'', 'email'=>'', 'course'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id=?');
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) { http_response_code(404); exit('Student not found.'); }
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
    $name = trim($_POST['name'] ?? '');
    $roll = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $student = ['name'=>$name, 'roll_number'=>$roll, 'email'=>$email, 'course'=>$course];

    if ($name === '' || $roll === '' || $course === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter all fields and a valid email address.';
    } else {
        try {
            if ($postedId) {
                $stmt = $pdo->prepare('UPDATE students SET name=?, roll_number=?, email=?, course=? WHERE id=?');
                $stmt->execute([$name, $roll, $email, $course, $postedId]);
                $studentId = $postedId;
            } else {
                $stmt = $pdo->prepare('INSERT INTO students (name, roll_number, email, course) VALUES (?,?,?,?)');
                $stmt->execute([$name, $roll, $email, $course]);
                $studentId = (int)$pdo->lastInsertId();
            }

            if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['document'];
                if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload failed. Check the file size and try again.');
                if ($file['size'] > MAX_UPLOAD_BYTES) throw new RuntimeException('File must be 5 MB or smaller.');

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $allowed = ['application/pdf'=>'pdf', 'image/jpeg'=>'jpg', 'image/png'=>'png'];
                if (!isset($allowed[$mime])) throw new RuntimeException('Only PDF, JPG and PNG files are accepted.');

                $original = basename($file['name']);
                $stored = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                $destination = UPLOAD_DIR . DIRECTORY_SEPARATOR . $stored;
                if (file_exists($destination)) throw new RuntimeException('Generated filename already exists; upload again.');
                if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('Could not store the uploaded file.');

                $relativePath = 'uploads/' . $stored;
                $stmt = $pdo->prepare('INSERT INTO student_files (student_id, stored_name, original_name, file_path, file_type) VALUES (?,?,?,?,?)');
                $stmt->execute([$studentId, $stored, $original, $relativePath, $mime]);
            }

            $_SESSION['notice'] = 'Student saved successfully.';
            header('Location: index.php'); exit;
        } catch (PDOException $e) {
            $error = 'Could not save the record. Check whether the roll number already exists.';
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Edit' : 'Add' ?> Student</title><link rel="stylesheet" href="style.css"></head><body>
<main class="container narrow"><h1><?= $id ? 'Edit' : 'Add' ?> Student</h1>
<?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel">
<input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= (int)($id ?? 0) ?>">
<label>Full name<input name="name" required maxlength="120" value="<?= h($student['name'] ?? '') ?>"></label>
<label>Roll number<input name="roll_number" required maxlength="40" value="<?= h($student['roll_number'] ?? '') ?>"></label>
<label>Email<input name="email" type="email" required maxlength="160" value="<?= h($student['email'] ?? '') ?>"></label>
<label>Course<input name="course" required maxlength="100" value="<?= h($student['course'] ?? '') ?>"></label>
<label>Profile document (PDF/JPG/PNG, max 5 MB)<input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"></label>
<p class="muted">Uploading a new file adds a file record; it does not overwrite an existing file.</p>
<button type="submit">Save student</button> <a href="index.php">Cancel</a>
</form></main></body></html>
