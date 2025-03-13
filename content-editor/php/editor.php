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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor</title>
    <link rel="stylesheet" href="../css/editor.css">
    <script src="https://editor.unlayer.com/embed.js"></script>
</head>
<body>
<?php include('../../category/php/header.php'); ?>

    <h1 style="margin-top: 120px; font-size: 30px;">Edit</h1>
    <div class="breadcrumbs-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" id="editor-breadcrumb" style="padding-left: 20px;">
            <li class="breadcrumb-item"><a href="../../category/php/index.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./content-list.php">รายการเนื้อหา</a></li>
            <li class="breadcrumb-item active" aria-current="page">แก้ไขเนื้อหา</li>
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


    <div id="editor-container" style="height: 500px; border: 1px solid #ccc;"></div>
    <button id="save-button">บันทึก</button>
    <p id="status"></p>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id'); 
        const editorContainer = document.getElementById('editor-container');
        const statusElement = document.getElementById('status');

        if (!id) {
            statusElement.textContent = 'กรุณากรอกข้อมูลเพื่อสร้างเนื้อหาใหม่';
            statusElement.style.color = 'blue';
        }

        unlayer.init({
            id: 'editor-container',
            projectId: 0,
            displayMode: 'email'
        });

        if (id) {
            fetch(`./get-content.php?id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        try {
                            if (data.design) {
                                unlayer.loadDesign(data.design); 
                            } else if (data.content) {
                                unlayer.setHTML(data.content);
                            } else {
                                throw new Error("ไม่มีข้อมูลที่สามารถโหลดได้");
                            }

                            document.getElementById("name").value = data.name || "";
                            document.getElementById("primary-topic").value = data.primary_topic || "";
                            document.getElementById("secondary-topic").value = data.secondary_topic || "";
                            document.getElementById("tertiary-topic").value = data.tertiary_topic || "";
                            document.getElementById("quaternary-topic").value = data.quaternary_topic || "";

                        } catch (error) {
                            console.error("Error loading design into Unlayer:", error);
                            statusElement.textContent = "เกิดข้อผิดพลาดในการโหลดเนื้อหาเข้าสู่ editor";
                            statusElement.style.color = "red";
                        }
                    } else {
                        statusElement.textContent = `ไม่พบเนื้อหาสำหรับ ID ${id}`;
                        statusElement.style.color = "red";
                    }
                })
                .catch(error => {
                    console.error("Error loading content:", error);
                    statusElement.textContent = "เกิดข้อผิดพลาดในการโหลดเนื้อหา";
                    statusElement.style.color = "red";
                });
        }

        function saveContent() {
            statusElement.textContent = 'กำลังบันทึก...';
            statusElement.style.color = 'blue';

            const name = document.getElementById('name').value;
            const primaryTopic = document.getElementById('primary-topic').value.trim();
            const secondaryTopic = document.getElementById('secondary-topic').value.trim();
            const tertiaryTopic = document.getElementById('tertiary-topic').value.trim();
            const quaternaryTopic = document.getElementById('quaternary-topic').value.trim();

            if (!name || !primaryTopic ) {
                statusElement.textContent = 'กรุณากรอกข้อมูลให้ครบถ้วนในส่วนที่จำเป็น';
                statusElement.style.color = 'red';
                return;
            }

            unlayer.exportHtml((data) => {
                const htmlContent = data.html;
                unlayer.saveDesign((design) => {
                    const postData = {
                        id: id || null,
                        name,
                        content: htmlContent,
                        design: JSON.stringify(design),
                        primary_topic: primaryTopic || null,
                        secondary_topic: secondaryTopic || null,
                        tertiary_topic: tertiaryTopic || null,
                        quaternary_topic: quaternaryTopic || null,
                    };

                    console.log("Sending data:", postData);

                    fetch('./save-content.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(postData)
                    })
                    .then(response => response.json())
                    .then(result => {
                        console.log("Server response:", result);
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

        document.getElementById('save-button').addEventListener('click', function(event) {
        if (!confirm("คุณแน่ใจหรือไม่ว่าต้องการบันทึกเนื้อหานี้?")) {
            event.preventDefault();
            return;
        }
        saveContent();
    });
    </script>
</body>
</html>