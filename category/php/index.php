<?php
require_once 'db.php';

// Function to get topics from the database
function getTopics($level, $parentValue = null) {
    $pdo = getPDO();
    $query = "SELECT * FROM editor_content WHERE ";

    // Define query for each level
    if ($level === 'main') {
        $query .= "main_topic IS NOT NULL AND sub_topic IS NULL AND sub_sub_topic IS NULL";
    } elseif ($level === 'sub') {
        $query .= "main_topic = :main_topic AND sub_topic IS NOT NULL AND sub_sub_topic IS NULL";
    } elseif ($level === 'sub_sub') {
        $query .= "main_topic = :main_topic AND sub_topic = :sub_topic AND sub_sub_topic IS NOT NULL";
    } else {
        return []; // Return empty array if the level is invalid
    }

    $stmt = $pdo->prepare($query);

    // Bind parameters for each level
    if ($level === 'sub') {
        $stmt->execute([':main_topic' => $parentValue]);
    } elseif ($level === 'sub_sub') {
        list($mainTopic, $subTopic) = explode("\t", $parentValue); // Split values for sub_sub
        $stmt->execute([
            ':main_topic' => $mainTopic,
            ':sub_topic' => $subTopic,
        ]);
    } else {
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to display topics in a hierarchical structure
function displayTopics($level, $parentValue = null) {
    $topics = getTopics($level, $parentValue);

    foreach ($topics as $topic) {
        $topicValue = $level === 'main' ? $topic['main_topic'] : (
            $level === 'sub' ? $topic['main_topic'] . "\t" . $topic['sub_topic'] : 
            $topic['main_topic'] . "\t" . $topic['sub_topic'] . "\t" . $topic['sub_sub_topic']
        );

        echo '<li>';
        echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($topic[$level . '_topic']) . '</a>';

        // Display content if available
        if ($level === 'sub_sub' && isset($topic['content']) && !empty($topic['content'])) {
            echo '<div class="content-display">';
            echo htmlspecialchars($topic['content']); // Display content for sub_sub_topic
            echo '</div>';
        }

        // Display child topics if applicable
        if ($level === 'main') {
            echo '<ul style="display: none; padding-left: 20px;">';
            displayTopics('sub', $topic['main_topic']);
            echo '</ul>';
        } elseif ($level === 'sub') {
            echo '<ul style="display: none; padding-left: 20px;">';
            displayTopics('sub_sub', $topicValue);
            echo '</ul>';
        }

        echo '</li>';
    }
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

        function loadContent(projectId) {
            fetch('../php/content.php?project_id=' + projectId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                });
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Header</h2>
        <ul>
            <?php displayTopics('main'); ?>
        </ul>
    </div>
    <div class="content" id="content-area">
        <h1>Welcome</h1>
        <p>Select a topic from the sidebar to view details.</p>
    </div>
</body>
</html>
