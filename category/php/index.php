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
    $dbname = "test02";

    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, title, primary_topic, secondary_topic, tertiary_topic, quaternary_topic, content FROM editor_content ORDER BY category_id";
    $result = $conn->query($sql);

    $topics = [];

    while ($row = $result->fetch_assoc()) {
        $primary = $row['primary_topic'] ?: $row['title'];
        $secondary = $row['secondary_topic'];
        $tertiary = $row['tertiary_topic'];
        $quaternary = $row['quaternary_topic'];
        $content = $row['content'];

        if (!isset($topics[$primary])) {
            $topics[$primary] = ['content' => '', 'id' => null, 'subtopics' => []];
        }

        if (empty($secondary) && empty($tertiary) && empty($quaternary)) {
            $topics[$primary]['content'] = $content;
            $topics[$primary]['id'] = $row['id'];
        }

        if ($secondary) {
            if (!isset($topics[$primary]['subtopics'][$secondary])) {
                $topics[$primary]['subtopics'][$secondary] = [
                    'content' => '', 
                    'id' => null, 
                    'subtopics' => []];
            }

            if (empty($tertiary) && empty($quaternary)) {
                $topics[$primary]['subtopics'][$secondary]['content'] = $content;
                $topics[$primary]['subtopics'][$secondary]['id'] = $row['id'];
            }

            if ($tertiary) {
                if (!isset($topics[$primary]['subtopics'][$secondary]['subtopics'][$tertiary])) {
                    $topics[$primary]['subtopics'][$secondary]['subtopics'][$tertiary] = [
                        'content' => '',
                        'id' => null,
                        'subtopics' => []
                    ];
                }

                if (empty($quaternary)) {
                    $topics[$primary]['subtopics'][$secondary]['subtopics'][$tertiary]['content'] = $content;
                    $topics[$primary]['subtopics'][$secondary]['subtopics'][$tertiary]['id'] = $row['id'];
                }

                if ($quaternary) {
                    $topics[$primary]['subtopics'][$secondary]['subtopics'][$tertiary]['subtopics'][] = [
                        'id' => $row['id'],
                        'title' => $quaternary,
                        'content' => $content
                    ];
                }
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
        if (subContainer && (subContainer.classList.contains('sub-container') || 
                            subContainer.classList.contains('sub-sub-container') ||
                            subContainer.classList.contains('sub-sub-sub-container'))) {
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

        <?php foreach ($topics as $primary_topic => $primary_data): ?>
            <div class="topic main-topic" 
                onclick="handleTopicClick(this, <?php echo $primary_data['id'] ? $primary_data['id'] : 'null'; ?>, event)">
                <span><?php echo htmlspecialchars($primary_topic, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="sub-container">
                <?php foreach ($primary_data['subtopics'] as $secondary_topic => $secondary_data): ?>
                    <div class="topic sub-topic"
                        onclick="handleTopicClick(this, <?php echo $secondary_data['id'] ? $secondary_data['id'] : 'null'; ?>, event)">
                        <span><?php echo htmlspecialchars($secondary_topic, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="sub-sub-container">
                        <?php foreach ($secondary_data['subtopics'] as $tertiary_topic => $tertiary_data): ?>
                            <div class="topic sub-sub-topic" 
                                onclick="handleTopicClick(this, <?php echo $tertiary_data['id'] ? $tertiary_data['id'] : 'null'; ?>, event)">
                                <span><?php echo htmlspecialchars($tertiary_topic, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="sub-sub-sub-container">
                                <?php foreach ($tertiary_data['subtopics'] as $quaternary_topic): ?>
                                    <div class="topic sub-sub-sub-topic" 
                                        onclick="handleTopicClick(this, <?php echo $quaternary_topic['id']; ?>, event)">
                                        <span><?php echo htmlspecialchars($quaternary_topic['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Content Area -->
    <div class="content-area" id="content-area">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .content-box {
            display: flex;
            align-items: center;
            justify-content: center;
            /* height: 100vh; */
            background: #f8f9fa;
        }
        .container-custom {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .text-section {
            flex: 1;
            padding-right: 30px;
        }
        .text-section h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .text-section p{
            font-size: 1.5rem;
        }
        .grid-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
        }
        .grid-item {
            background: #e9ecef;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            font-size: 1rem;
            transition: 0.3s;
        }
        .grid-item:hover {
            background: #dee2e6;
            transform: translateY(-5px);
        }
        .grid-item img {
            width: 40px;
            margin-bottom: 10px;
        }
        .image-section img {
            width: 550px;
            border-radius: 8px;
        }
    </style>

<div class="content-box">
    <div class="container-custom">
        <!-- ส่วนข้อความ -->
        <div class="text-section">
            <h1>Wealth Management System Limited</h1><br>
            <ul><p>เอกสารประกอบการติดตั้งโปรแกรม</p> <br><br><br></ul>
            
            <!-- ตารางการ์ด -->
            <div class="grid-section">
                <div class="grid-item" onclick="window.location.href='../../content-editor/html/content-list.html'">
                    <img src="../../assets/checklist.png" alt="Image">
                    <p>Content-list</p>
                </div>
                <div class="grid-item" onclick="window.location.href='../../content-editor/html/add_data.html'">
                    <img src="../../assets/edit.png" alt="icon">
                    <p>Add Data</p>
                </div>

            </div>
        </div>

        <!-- ส่วนรูปภาพ -->
        <div class="image-section">
            <img src="../../assets/images.png" alt="Image">
        </div>
    </div>
</div>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </div>

</body>
</html>