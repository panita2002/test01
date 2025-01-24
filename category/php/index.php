<?php
require_once 'db.php';

// Function to get headings
function getHeadings($parentId = null) {
    $pdo = getPDO();
    $query = "SELECT * FROM headings WHERE parent_id " . ($parentId ? "= ?" : "IS NULL") . " ORDER BY `order`";
    $stmt = $pdo->prepare($query);
    $stmt->execute($parentId ? [$parentId] : []);
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
        function toggleDropdown(element) {
            const sublist = element.nextElementSibling;
            if (sublist.style.display === "block") {
                sublist.style.display = "none";
            } else {
                sublist.style.display = "block";
            }
        }

        function loadContent(headingId) {
            fetch('../php/content.php?heading_id=' + headingId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                });
        }
        
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>wealth</h2>
        <h3>Version</h3>
            <select>
                <option>Visual Studio 2022</option>
                <option>Visual Studio 2019</option>
                <option>Visual Studio 2017</option>
                <option>Visual Studio 2015</option>
            </select>
        <ul>
            <?php
            function displaySidebar($parentId = null) {
                $headings = getHeadings($parentId);
                foreach ($headings as $heading) {
                    echo '<li>';
                    echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($heading['title']) . '</a>';
                    
                    // ตรวจสอบว่ามีหัวข้อย่อยหรือไม่
                    $subHeadings = getHeadings($heading['id']);
                    if (!empty($subHeadings)) {
                        echo '<ul style="display: none; padding-left: 20px;">';
                        foreach ($subHeadings as $subHeading) {
                            echo '<li>';
                            echo '<a href="#" onclick="loadContent(' . $subHeading['id'] . '); return false;">' . htmlspecialchars($subHeading['title']) . '</a>';
                            
                            // ตรวจสอบว่ามีหัวข้อย่อยของย่อยหรือไม่
                            $subSubHeadings = getHeadings($subHeading['id']);
                            if (!empty($subSubHeadings)) {
                                echo '<ul style="display: none; padding-left: 20px;">';
                                foreach ($subSubHeadings as $subSubHeading) {
                                    echo '<li>';
                                    echo '<a href="#" onclick="loadContent(' . $subSubHeading['id'] . '); return false;">' . htmlspecialchars($subSubHeading['title']) . '</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                            }

                            echo '</li>';
                        }
                        echo '</ul>';
                    }
                    echo '</li>';
                }
            }

            displaySidebar();
            ?>
        </ul>
    </div>
    <div class="content" id="content-area">
        <h1>Welcome</h1>
        <p>Select a heading from the sidebar to view details.</p>
    </div>
</body>
</html>
