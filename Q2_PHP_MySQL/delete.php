<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('POST required.'); }
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT stored_name FROM student_files WHERE student_id=?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $row) {
    $path = UPLOAD_DIR . DIRECTORY_SEPARATOR . basename($row['stored_name']);
    if (is_file($path)) unlink($path);
}
$stmt = $pdo->prepare('DELETE FROM students WHERE id=?');
$stmt->execute([$id]);
$_SESSION['notice'] = 'Student and associated file records deleted.';
header('Location: index.php');
exit;
?>
