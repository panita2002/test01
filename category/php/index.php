<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบแสดงเอกสาร</title>
    <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test01";

    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, title, main_topic, sub_topic, sub_sub_topic,sub_sub_sub_topic, content FROM editor_content ORDER BY category_id";
    $result = $conn->query($sql);

    $topics = [];


    while ($row = $result->fetch_assoc()) {
        $main = $row['main_topic'] ?: $row['title'];
        $sub = $row['sub_topic'];
        $sub_sub = $row['sub_sub_topic'];
        $sub_sub_sub = $row['sub_sub_sub_topic'];
        $content = $row['content'];

        if (!isset($topics[$main])) {
            $topics[$main] = ['content' => '', 'id' => null, 'subtopics' => []];
        }

        if (empty($sub) && empty($sub_sub)) {
            $topics[$main]['content'] = $content;
            $topics[$main]['id'] = $row['id'];
        }

        if ($sub) {
            if (!isset($topics[$main]['subtopics'][$sub])) {
                $topics[$main]['subtopics'][$sub] = ['content' => '', 'id' => null, 'subtopics' => []];
            }

            if (empty($sub_sub)) {
                $topics[$main]['subtopics'][$sub]['content'] = $content;
                $topics[$main]['subtopics'][$sub]['id'] = $row['id'];
            }

            if ($sub_sub) {
                $topics[$main]['subtopics'][$sub]['subtopics'][] = [
                    'id' => $row['id'],
                    'title' => $sub_sub,
                    'content' => $content
                ];
            }
        }
    }

    $conn->close();
    ?>

    <script>
    function handleTopicClick(element, id, event) {
        event.stopPropagation();
        
        // Remove active class from all topics
        document.querySelectorAll('.topic').forEach(topic => {
            topic.classList.remove('active-topic');
        });
        
        // Add active class to clicked topic
        element.classList.add('active-topic');
        
        // Load content if ID exists
        if (id) {
            loadContent(id);
        }

        // Toggle dropdown if it exists
        const subContainer = element.nextElementSibling;
        if (subContainer && (subContainer.classList.contains('sub-container') || subContainer.classList.contains('sub-sub-container'))) {
            if (subContainer.style.display === "none" || subContainer.style.display === "") {
                subContainer.style.display = "block";
            } else {
                subContainer.style.display = "none";
            }
        }
    }

    function loadContent(id) {
        fetch(`./get-content.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('content-area').innerHTML = data.content;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('content-area').innerHTML = 'เกิดข้อผิดพลาดในการโหลดเนื้อหา';
            });
    }
    </script>

    <?php foreach ($topics as $main_topic => $main_data): ?>
        <div class="topic main-topic" 
             onclick="handleTopicClick(this, <?php echo $main_data['id'] ? $main_data['id'] : 'null'; ?>, event)">
            <span><?php echo htmlspecialchars($main_topic, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="sub-container">
            <?php foreach ($main_data['subtopics'] as $sub_topic => $sub_data): ?>
                <div class="topic sub-topic"
                     onclick="handleTopicClick(this, <?php echo $sub_data['id'] ? $sub_data['id'] : 'null'; ?>, event)">
                    <span><?php echo htmlspecialchars($sub_topic, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="sub-sub-container">
                    <?php foreach ($sub_data['subtopics'] as $sub_sub_topic): ?>
                        <div class="topic sub-sub-topic" 
                             onclick="handleTopicClick(this, <?php echo $sub_sub_topic['id']; ?>, event)">
                            <?php echo htmlspecialchars($sub_sub_topic['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Content Area -->
    <div class="content-area" id="content-area">
        กรุณาเลือกหัวข้อจากเมนูด้านซ้ายเพื่อดูเนื้อหา
    </div>

</body>
</html>