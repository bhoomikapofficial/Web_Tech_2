<?php
require __DIR__ . '/config.php';
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM student_files WHERE id=?');
$stmt->execute([$fileId]);
$file = $stmt->fetch();
if (!$file) { http_response_code(404); exit('File not found.'); }

$root = realpath(UPLOAD_DIR);
$path = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file['file_path']);
if (!$root || !$path || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path)) {
    http_response_code(404); exit('Stored file is missing.');
}
$allowed = ['application/pdf', 'image/jpeg', 'image/png'];
if (!in_array($file['file_type'], $allowed, true)) { http_response_code(415); exit('Unsupported file type.'); }

header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $file['file_type']);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . rawurlencode($file['original_name']) . '"');
readfile($path);
exit;
?>
