<?php
require_once 'db.php';
require_once 'insert.php'; // Import getHeadings function

/**
 * ฟังก์ชันสำหรับแสดง Sidebar แบบไดนามิก
 * @param int $projectId รหัสโปรเจกต์ที่ต้องการดึงข้อมูล
 */
function displaySidebar($projectId) {
    // ดึง Main Topics
    $mainTopics = getTopics($projectId);
    if (empty($mainTopics)) {
        echo '<li>No topics available.</li>';
        return;
    }

    foreach ($mainTopics as $mainTopic) {
        echo '<li>';
        echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($mainTopic['main_topic']) . '</a>';

        // ดึง Sub Topics
        $subTopics = getTopics($projectId, $mainTopic['main_topic']);
        if (!empty($subTopics)) {
            echo '<ul style="display: none; padding-left: 20px;">';
            foreach ($subTopics as $subTopic) {
                echo '<li>';
                echo '<a href="#" onclick="toggleDropdown(this); return false;">' . htmlspecialchars($subTopic['sub_topic']) . '</a>';

                // ดึง Sub-Sub Topics
                $subSubTopics = getTopics($projectId, $mainTopic['main_topic'], $subTopic['sub_topic']);
                if (!empty($subSubTopics)) {
                    echo '<ul style="display: none; padding-left: 20px;">';
                    foreach ($subSubTopics as $subSubTopic) {
                        echo '<li>';
                        echo '<a href="#" onclick="loadContent(' . htmlspecialchars($subSubTopic['id']) . '); return false;">' . htmlspecialchars($subSubTopic['sub_sub_topic']) . '</a>';
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
