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
    <title>View Content</title>
    <link rel="stylesheet" href="../css/view.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <?php include('../../category/php/header.php'); ?>

    <h1 style="margin-top: 120px; font-size: 30px;">รายละเอียดเนื้อหา</h1>

    <div id="breadcrumbs-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="padding-left: 20px;">
            <li class="breadcrumb-item"><a href="../../category/php/index.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./content-list.php">รายการเนื้อหา</a></li>
            <li class="breadcrumb-item active" aria-current="page">ดูเนื้อหา</li>
        </ol>
    </nav>
    </div>

    <div id="content">กำลังโหลดเนื้อหา...</div>
    <button id="edit-btn">แก้ไข</button>

    <script>
        const params = new URLSearchParams(window.location.search);
        const id = params.get('id');

        if (!id) {
            document.getElementById('content').innerHTML = '<p style="color: red;">ไม่พบข้อมูล</p>';
            document.getElementById('edit-btn').style.display = 'none';
        } else {
            fetch(`./get-content.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('content').innerHTML = data.content;
                    } else {
                        document.getElementById('content').innerHTML = '<p style="color: red;">ไม่พบข้อมูล</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('content').innerHTML = '<p style="color: red;">ไม่สามารถโหลดข้อมูลได้</p>';
                });

            document.getElementById('edit-btn').addEventListener('click', () => {
                window.location.href = `./editor.php?id=${id}`;
            });
        }

if (id) {
    fetch(`./get-content.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.name) {
                const breadcrumb = document.querySelector('.breadcrumb');
                const lastItem = breadcrumb.querySelector('.breadcrumb-item.active');
                lastItem.textContent = data.name;
            }
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาดในการอัพเดต breadcrumbs:', error);
        });
}
    </script>
</body>
</html>