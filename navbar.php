<link rel="stylesheet" href="nav.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<nav class="navbar">
    <div class="navbar-container">
        <a href="#" class="navbar-logo" onclick="showSection('my-cvs')">Mycvplus</a><br>
        <ul class="navbar-menu">
            <li><a href="#" onclick="showSection('create-cv')">Créer CV</a></li>
            <li><a href="#" onclick="showSection('add-experience')">Ajouter Expérience</a></li>
            <li><a href="#" onclick="showSection('add-formation')">Ajouter Formation</a></li>
            <li><a href="#" onclick="showSection('my-cvs')">Mes CV</a></li>
            
            <?php if (isset($_SESSION['id_user'])): ?>
                <!-- Display "Déconnexion" if the user is logged in -->
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <!-- Display "Login" and "Sign Up" if the user is not logged in -->
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
