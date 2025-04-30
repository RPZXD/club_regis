<?php
require __DIR__ . '/../vendor/autoload.php';

use App\DatabaseDocuments;

$db = new DatabaseDocuments();
$documents = $db->fetchAll("SELECT * FROM documents ORDER BY upload_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document List</title>
</head>
<body>
    <h2>Document List</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Upload Date</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($documents as $doc): ?>
            <tr>
                <td><?= htmlspecialchars($doc['id']) ?></td>
                <td><?= htmlspecialchars($doc['name']) ?></td>
                <td><?= htmlspecialchars($doc['upload_date']) ?></td>
                <td>
                    <a href="/uploads/<?= rawurlencode($doc['path']) ?>" download>
                        Download
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
