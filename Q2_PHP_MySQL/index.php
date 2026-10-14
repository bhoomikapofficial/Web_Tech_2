<?php
require __DIR__ . '/config.php';

$students = $pdo->query(
    'SELECT s.*, (SELECT COUNT(*) FROM student_files f WHERE f.student_id=s.id) AS file_count
     FROM students s ORDER BY s.id DESC'
)->fetchAll();
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Admin — Q2</title><link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
  <h1>Student Admin Panel</h1>
  <p>CRUD operations, file uploads and stored file metadata.</p>
  <?php if ($notice): ?><p class="notice"><?= h($notice) ?></p><?php endif; ?>
  <p><a class="button" href="student_form.php">+ Add student</a></p>
  <div class="table-wrap"><table>
    <thead><tr><th>Name</th><th>Roll number</th><th>Email</th><th>Course</th><th>Files</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($students as $s): ?>
      <tr>
        <td><?= h($s['name']) ?></td><td><?= h($s['roll_number']) ?></td>
        <td><?= h($s['email']) ?></td><td><?= h($s['course']) ?></td><td><?= (int)$s['file_count'] ?></td>
        <td class="actions">
          <a href="view.php?id=<?= (int)$s['id'] ?>">View</a>
          <a href="student_form.php?id=<?= (int)$s['id'] ?>">Edit</a>
          <form method="post" action="delete.php" onsubmit="return confirm('Delete this student and associated file records?')">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="link-button" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$students): ?><tr><td colspan="6">No student records yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</main>
</body></html>
