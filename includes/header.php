<header class="header">
    <div class="container">
        <div class="header-content">
            <img src="public/images/logo.jpg
                " width="90" height="100">
</h1>Memory Capsule</a>
            
            <nav>
                <ul class="nav-menu">
                    <?php if (isLoggedIn()): ?>
                        <li><a href="index.php" class="nav-link">Dashboard</a></li>
                        <li><a href="create-capsule.php" class="nav-link">Create Capsule</a></li>
                        <li><a href="friends.php" class="nav-link">Friends</a></li>
                        <li><a href="profile.php" class="nav-link">Profile</a></li>
                        <li><a href="logout.php" class="nav-link">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="login.php" class="nav-link">Login</a></li>
                        <li><a href="register.php" class="nav-link">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>