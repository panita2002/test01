<body>
<div class="content-area" id="content-area">
    <?php 
    if (!isset($_GET['id'])) {
        include '../php/breadcrumbs.php';
    }
    ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/content.css">
    

<div class="content-box">
    <div class="container-custom">
        <div class="text-section">
            <h1>Wealth Management System Limited</h1>
            <p>คู่มือการติดตั้งของ Product Investment </p> <br>
            
            <div class="grid-section">
                <div class="grid-item" onclick="window.location.href='../../content-editor/php/content-list.php'">
                    <img src="../../assets/checklist.png" alt="Image">
                    <p>Content-list</p>
                </div>
                <div class="grid-item" onclick="window.location.href='../../content-editor/php/add_data.php'">
                    <img src="../../assets/edit.png" alt="Image">
                    <p>Add Data</p>
                </div>
            </div>
        </div>

        <div class="image-section">
            <img src="../../assets/images1.png" alt="Image">
        </div>
    </div>
</div>
</body>