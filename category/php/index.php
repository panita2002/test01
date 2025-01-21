<?php
require '../db/db.php';

// Function to get headings
function getHeadings($parentId = null) {
    $pdo = getPDO();
    $query = "SELECT * FROM headings WHERE parent_id " . ($parentId ? "= ?" : "IS NULL") . " ORDER BY `order`";
    $stmt = $pdo->prepare($query);
    $stmt->execute($parentId ? [$parentId] : []);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get tables by heading
function getTablesByHeading($headingId) {
    $pdo = getPDO();
    $query = "SELECT * FROM tables WHERE heading_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$headingId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get rows of a table
function getTableRows($tableId) {
    $pdo = getPDO();
    $query = "SELECT * FROM table_rows WHERE table_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tableId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Viewer</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function loadContent(headingId) {
            fetch('content.php?heading_id=' + headingId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                });
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Headings</h2>
        <?php
        function displaySidebar($parentId = null) {
            $headings = getHeadings($parentId);
            foreach ($headings as $heading) {
                echo '<a href="#" onclick="loadContent(' . $heading['id'] . '); return false;">' . htmlspecialchars($heading['title']) . '</a>';
                displaySidebar($heading['id']);
            }
        }

        displaySidebar();
        ?>
    </div>
    <div class="content" id="content-area">
        <h1>Welcome</h1>
        <p>Select a heading from the sidebar to view details.</p>
    </div>
</body>
</html>
