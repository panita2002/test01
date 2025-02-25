<div class="sidebar">
    <link rel="stylesheet" href="../css/sidebar.css">
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

    $sql = "SELECT id, primary_topic, secondary_topic, tertiary_topic, quaternary_topic, content FROM editor_content ORDER BY category_id";
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
        
        document.querySelectorAll('.topic').forEach(topic => {
            topic.classList.remove('active-topic');
        });
        
        element.classList.add('active-topic');
        
        if (id) {
            loadContent(id);
        }

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

    function loadContent(id, topics) {
    const queryParams = new URLSearchParams(window.location.search);
    queryParams.set('id', id);
    
    window.history.pushState({}, '', `${window.location.pathname}?${queryParams.toString()}`);
    
    fetch(`./get-content.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            fetch(`./breadcrumbs.php?id=${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(breadcrumbsData => {
                    const contentArea = document.getElementById('content-area');
                    
                    let breadcrumbsHtml = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
                    breadcrumbsData.forEach((item, index) => {
                        if (index === 0) {
                            breadcrumbsHtml += `<li class="breadcrumb-item"><a href="${item.url}">${item.title}</a></li>`;
                        } else if (index < breadcrumbsData.length - 1) {
                            breadcrumbsHtml += `<li class="breadcrumb-item"><span>${item.title}</span></li>`;
                        } else {
                            breadcrumbsHtml += `<li class="breadcrumb-item active" aria-current="page">${item.title}</li>`;
                        }
                    });
                    breadcrumbsHtml += '</ol></nav>';
                    
                    const contentHtml = data.content;
                    contentArea.innerHTML = breadcrumbsHtml + contentHtml;
                })
                .catch(error => {
                    console.error('Breadcrumbs Error:', error);
                    document.getElementById('content-area').innerHTML = data.content;
                });
        })
        .catch(error => {
            console.error('Content Error:', error);
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