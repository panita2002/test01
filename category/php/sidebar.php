<?php
require_once 'db.php';
require_once 'insert.php'; // Import getHeadings

function displaySidebar($projectId) {
    $mainTopics = getHeadings($projectId);
    foreach ($mainTopics as $mainTopic) {
        echo '<li>';
        echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($mainTopic['main_topic']) . '</a>';

        // Get Sub Topics
        $subTopics = getHeadings($projectId, $mainTopic['main_topic']);
        if (!empty($subTopics)) {
            echo '<ul style="display: none; padding-left: 20px;">';
            foreach ($subTopics as $subTopic) {
                echo '<li>';
                echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($subTopic['sub_topic']) . '</a>';

                // Get Sub-Sub Topics
                $subSubTopics = getHeadings($projectId, $mainTopic['main_topic'], $subTopic['sub_topic']);
                if (!empty($subSubTopics)) {
                    echo '<ul style="display: none; padding-left: 20px;">';
                    foreach ($subSubTopics as $subSubTopic) {
                        echo '<li>';
                        echo '<a href="#" onclick="loadContent(' . $subSubTopic['id'] . '); return false;">' . htmlspecialchars($subSubTopic['sub_sub_topic']) . '</a>';
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
?>
