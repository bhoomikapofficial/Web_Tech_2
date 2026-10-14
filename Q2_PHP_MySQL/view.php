<?php
require __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM students WHERE id=?');
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { http_response_code(404); exit('Student not found.'); }
$stmt = $pdo->prepare('SELECT * FROM student_files WHERE student_id=? ORDER BY uploaded_at DESC');
$stmt->execute([$id]);
$files = $stmt->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student details</title><link rel="stylesheet" href="style.css"></head><body><main class="container narrow">
<h1>Student Details</h1><section class="panel">
<p><strong>Name:</strong> <?= h($student['name']) ?></p>
<p><strong>Roll number:</strong> <?= h($student['roll_number']) ?></p>
<p><strong>Email:</strong> <?= h($student['email']) ?></p>
<p><strong>Course:</strong> <?= h($student['course']) ?></p>
<p><strong>Created:</strong> <?= h($student['created_at']) ?></p>
</section><h2>Uploaded files</h2>
<?php if (!$files): ?><p>No files uploaded.</p><?php else: ?><ul>
<?php foreach ($files as $f): ?>
<li><?= h($f['original_name']) ?> — <?= h($f['file_type']) ?> — <?= h($f['uploaded_at']) ?>
(<?= number_format((int)(@filesize(__DIR__ . '/' . $f['file_path'])) / 1024, 1) ?> KB)
<a href="download.php?id=<?= (int)$f['id'] ?>">Open/download</a></li>
<?php endforeach; ?></ul><?php endif; ?>
<a href="index.php">Back to list</a> | <a href="student_form.php?id=<?= (int)$student['id'] ?>">Edit</a>
</main></body></html>
