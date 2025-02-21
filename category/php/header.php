<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/test01/category/css/header.css">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="/test01/category/php/index.php">
                <img src="../../assets/logo2.png" alt="Logo" width="250" height="60" class="d-inline-block align-text-top">
            </a>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/test01/category/php/index.php">Home</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 50px;">
                        Documents
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../../content-editor/php/content-list.php">Content List</a></li>
                        <li><a class="dropdown-item" href="../../content-editor/php/add_data.php">Add Data</a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['id'])): ?>
                    <span class="text-white me-3">👋 สวัสดี, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
                    <a href="../../login/php/logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="../../login/html/login.html" class="btn btn-success">Login</a>
                <?php endif; ?>

            </div>
        </div>
    </nav>
    
</header>
