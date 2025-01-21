<?php
require '../db/db.php';

// Ensure the helper functions are available
if (!function_exists('getTablesByHeading')) {
    function getTablesByHeading($headingId) {
        $pdo = getPDO();
        $query = "SELECT * FROM tables WHERE heading_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$headingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('getTableRows')) {
    function getTableRows($tableId) {
        $pdo = getPDO();
        $query = "SELECT * FROM table_rows WHERE table_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$tableId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['heading_id'])) {
    $headingId = $_GET['heading_id'];

    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT title FROM headings WHERE id = ?");
    $stmt->execute([$headingId]);
    $heading = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($heading) {
        echo "<h2>" . htmlspecialchars($heading['title']) . "</h2>";

        $tables = getTablesByHeading($headingId);
        foreach ($tables as $table) {
            echo "<h3>" . htmlspecialchars($table['title']) . "</h3>";
            echo "<table>";
            echo "<tr><th>Variable</th><th>Value</th><th>Description</th></tr>";

            $rows = getTableRows($table['id']);
            foreach ($rows as $row) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['variable']) . "</td>
                        <td>" . htmlspecialchars($row['value']) . "</td>
                        <td>" . htmlspecialchars($row['description']) . "</td>
                      </tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>No content found for this heading.</p>";
    }
}
?>
