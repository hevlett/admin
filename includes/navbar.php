<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->


    <a class="navbar-brand ps-3" href="dashboard.php">Payroll Management</a>

    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <!-- You can add search input or other elements here if needed -->
        </div>
    </form>

    <!-- Navbar User Options -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <?php
        // Display user-related options if logged in
        if (isset($_SESSION['loggedin']) && isset($_SESSION['loggedinUser']['name'])) {
            echo '<li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-user fa-fw"></i> ' . $_SESSION['loggedinUser']['name'] . '
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Logout</a>
                </li>';
        }
        ?>
    </ul>
</nav>
