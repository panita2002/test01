<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://editor.unlayer.com/embed.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('../../category/php/header.php'); ?>

<h1 style="margin-top: 120px; font-size: 30px;">Add Data</h1>
<div class="breadcrumbs-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="padding-left: 20px;">
            <li class="breadcrumb-item"><a href="../../category/php/index.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item active" aria-current="page">เพิ่มเนื้อหาใหม่</li>
        </ol>
    </nav>
</div>

<form id="content-form">
    <div class="name-title-group form-group">
        <label for="name">Name:<span class="required">*</span></label>
        <input type="text" id="name" name="name" required>
    </div>

        

    <div class="topic-group">
        <label for="primary-topic">Primary Topic:<span class="required">*</span></label>
        <input type="text" id="primary-topic" name="primary-topic" class="autocomplete-field" required>
        
        <label for="secondary-topic">Secondary Topic:</label>
        <input type="text" id="secondary-topic" name="secondary-topic" class="autocomplete-field">
    </div>
    
    <div class="subsub-order-group">
        <label for="tertiary-topic">Tertiary Topic:</label>
        <input type="text" id="tertiary-topic" name="tertiary-topic" class="autocomplete-field">
        
        <label for="quaternary-topic">Quaternary Topic:</label>
        <input type="text" id="quaternary-topic" name="quaternary-topic" class="autocomplete-field">
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

    
        function setupAutocomplete(selector, type, extraData = {}) {
            $(selector).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "./get-topics.php",
                        dataType: "json",
                        data: { term: request.term, type: type, ...extraData },
                        success: function(data) {
                            response(data);
                        },
                        error: function() {
                            response([]);
                        }
                    });
                },
                minLength: 1
            });
        }

        setupAutocomplete("#primary-topic", "primary");
        setupAutocomplete("#secondary-topic", "secondary", { primary: $("#primary-topic").val() });
        setupAutocomplete("#tertiary-topic", "tertiary", { primary: $("#primary-topic").val(), secondary: $("#secondary-topic").val() });
        setupAutocomplete("#quaternary-topic", "quaternary", { primary: $("#primary-topic").val(), secondary: $("#secondary-topic").val(), tertiary: $("#tertiary-topic").val() });


    document.getElementById('save-button').addEventListener('click', function(event) {
        if (!confirm("คุณแน่ใจหรือไม่ว่าต้องการบันทึกเนื้อหานี้?")) {
            event.preventDefault();
            return;
        }
        saveContent();
    });

    function saveContent() {
        statusElement.textContent = 'กำลังบันทึก...';
        statusElement.style.color = 'blue';

        const name = document.getElementById('name').value;
        const primaryTopic = document.getElementById('primary-topic').value.trim();
        const secondaryTopic = document.getElementById('secondary-topic').value.trim();
        const tertiaryTopic = document.getElementById('tertiary-topic').value.trim();
        const quaternaryTopic = document.getElementById('quaternary-topic').value.trim();

        if (!name || !primaryTopic) {
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
                    content: htmlContent,
                    design: JSON.stringify(design),
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


</script>
</body>
</html>