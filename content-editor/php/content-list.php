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
    <title>Content List</title>
    <link rel="stylesheet" href="../css/content-list.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
</head>
<body>

<?php include('../../category/php/header.php'); ?>

<div class="container-list" style="margin-top: 120px;">
    <div class="container mt-4">
        <h1 class="text-center" style="font-size: 32px;">Content List</h1>

        <div class="breadcrumbs-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../category/php/index.php">หน้าหลัก</a></li>
                    <li class="breadcrumb-item active" aria-current="page">รายการเนื้อหา</li>
                </ol>
            </nav>
        </div>

        <button class="btn btn-primary mb-3" onclick="location.href='./add_data.php'">+ Add Data</button>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table">
                    <tr>
                        <th style="text-align: center;">ลำดับ</th>
                        <th style="text-align: center;">ชื่อเนื้อหา</th>
                        <th style="text-align: center;">วันที่สร้าง</th>
                        <th style="text-align: center;">การจัดการ</th>
                    </tr>                        
                </thead>
                <tbody id="content-list">
                    <tr>
                        <td colspan="4" class="text-center">กำลังโหลดข้อมูล...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    loadContentList();
});

function loadContentList() {
    fetch('./get-content-list.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('content-list');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4">ไม่มีข้อมูล</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', item.id);
                row.innerHTML = `
                    <td style="text-align: center;">${index + 1}</td>
                    <td>${item.name}</td>
                    <td>${item.created_at}</td>
                    <td>
                        <button1 onclick="viewContent(${item.id})">ดูรายละเอียด</button1>
                        <button2 onclick="editContent(${item.id})">แก้ไข</button2>
                        <button3 onclick="deleteContent(${item.id})" style="color: white;">ลบ</button3>
                    </td>
                `;
                tbody.appendChild(row);
            });

            new Sortable(tbody, {
                animation: 150,
                onEnd: function () {
                    saveOrder();
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('content-list').innerHTML = '<tr><td colspan="4" style="color: red;">ไม่สามารถโหลดข้อมูลได้</td></tr>';
        });
}

function saveOrder() {
    const rows = document.querySelectorAll("#content-list tr");
    const order = [];

    rows.forEach((row, index) => {
        order.push({
            id: row.getAttribute("data-id"),
            position: index + 1
        });
    });
    console.log(order); 
    fetch('./save-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order })
    })
    .then(response => response.json())
    .then(result => {
        if (!result.success) {
            alert(`เกิดข้อผิดพลาด: ${result.message}`);
        }
    })
    .catch(error => console.error('Error:', error));
}

function viewContent(id) {
    window.location.href = `./view-content.php?id=${id}`;
}

function editContent(id) {
    window.location.href = `./editor.php?id=${id}`;
}

function deleteContent(id) {
    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบเนื้อหานี้?')) {
        fetch(`./delete-content.php?id=${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('ลบเนื้อหาสำเร็จ');
                    loadContentList();
                } else {
                    alert(`เกิดข้อผิดพลาด: ${result.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการลบเนื้อหา');
            });
    }
}
</script>

</body>
</html>
