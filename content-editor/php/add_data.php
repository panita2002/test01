<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../../login/html/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มเนื้อหาใหม่</title>
    <link rel="stylesheet" href="../css/editor.css">
    <script src="https://editor.unlayer.com/embed.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<?php include('../../category/php/header.php'); ?>


    <h1 style="margin-top: 120px; font-size: 30px;">เพิ่มเนื้อหาใหม่</h1>

    <form id="content-form">
        <div class="name-title-group form-group">
            <label for="name">Name:<span class="required">*</span></label>
            <input type="text" id="name" name="name" required>
            
            <label for="title">Title:</label>
            <input type="text" id="title" name="title">
        </div>

        <div class="project-category-group form-group">
            <label for="project_id">Project ID:<span class="required">*</span></label>
            <input type="number" id="project_id" name="project_id" required>

            <label for="category_id">Category ID:<span class="required">*</span></label>
            <input type="number" id="category_id" name="category_id" required>
        </div>

        <div class="topic-group">
            <label for="primary-topic">Primary Topic:<span class="required">*</span></label>
            <input type="text" id="primary-topic" name="primary-topic" required>
            
            <label for="secondary-topic">Secondary Topic:</label>
            <input type="text" id="secondary-topic" name="secondary-topic">
        </div>
        
        <div class="subsub-order-group">
            <label for="tertiary-topic">Tertiary Topic:</label>
            <input type="text" id="tertiary-topic" name="tertiary-topic">
            
            <label for="quaternary-topic">Quaternary Topic:</label>
            <input type="text" id="quaternary-topic" name="quaternary-topic">
        </div>
    </form>

    <div id="editor-container" style="height: 500px; border: 1px solid #ccc; margin: 20px 0;"></div>
    
    <div class="button-group">
        <button id="save-button" class="primary-button">บันทึก</button>
        
    </div>
    
    <p id="status"></p>

    <script>
        const statusElement = document.getElementById('status');

        unlayer.init({
            id: 'editor-container',
            projectId: 0,
            displayMode: 'email'
        });

        statusElement.textContent = 'กรุณากรอกข้อมูลเพื่อสร้างเนื้อหาใหม่';
        statusElement.style.color = 'blue';

        function saveContent() {
            statusElement.textContent = 'กำลังบันทึก...';
            statusElement.style.color = 'blue';

            const name = document.getElementById('name').value;
            const title = document.getElementById('title').value;
            const projectId = parseInt(document.getElementById('project_id').value, 10);
            const categoryId = parseInt(document.getElementById('category_id').value, 10);
            const primaryTopic = document.getElementById('primary-topic').value.trim();
            const secondaryTopic = document.getElementById('secondary-topic').value.trim();
            const tertiaryTopic = document.getElementById('tertiary-topic').value.trim();
            const quaternaryTopic = document.getElementById('quaternary-topic').value.trim();

            
            if (!name || !primaryTopic || isNaN(projectId) || isNaN(categoryId)) {
                statusElement.textContent = 'กรุณากรอกข้อมูลให้ครบถ้วนในส่วนที่จำเป็น (ช่องที่มีเครื่องหมาย *)';
                statusElement.style.color = 'red';
                return;
            }

            unlayer.exportHtml((data) => {
                const htmlContent = data.html;
                unlayer.saveDesign((design) => {
                    const postData = {
                        id: null, 
                        name,
                        title: title || null,
                        content: htmlContent,
                        design: JSON.stringify(design),
                        project_id: projectId,
                        category_id: categoryId,
                        primary_topic: primaryTopic || null,
                        secondary_topic: secondaryTopic || null,
                        tertiary_topic: tertiaryTopic || null,
                        quaternary_topic: quaternaryTopic || null,
                    };

                    fetch('./save-content.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(postData)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            statusElement.textContent = 'บันทึกสำเร็จ!';
                            statusElement.style.color = 'green';
                            setTimeout(() => {
                                window.location.href = `../html/view-content.html?id=${result.id}`;
                            }, 1000);
                        } else {
                            statusElement.textContent = `เกิดข้อผิดพลาด: ${result.message}`;
                            statusElement.style.color = 'red';
                        }
                    })
                    .catch(error => {
                        console.error('Error saving content:', error);
                        statusElement.textContent = 'เกิดข้อผิดพลาดในการบันทึก';
                        statusElement.style.color = 'red';
                    });
                });
            });
        }

        document.getElementById('save-button').addEventListener('click', saveContent);
    </script>
</body>
</html>