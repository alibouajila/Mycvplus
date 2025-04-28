<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur MyCV</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="home-container">
    <div class="intro-text">
        <h1>Bienvenue sur Mycvplus</h1>
        <p>Créez, gérez et partagez vos CV en ligne de manière simple et efficace. Que vous soyez à la recherche d'emploi ou que vous souhaitiez simplement garder vos informations à jour, MyCV vous aide à organiser vos expériences professionnelles, vos formations et bien plus encore !</p>
    </div>

    <div class="cta-container">
        <h2>Déjà un compte ?</h2>
        <p>Si vous avez déjà un compte, vous pouvez vous connecter pour accéder à vos CV et gérer vos informations.</p>
        
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Se connecter</a>
            <a href="register.php" class="btn btn-secondary">Créer un compte</a>
        </div>
    </div>
</div>

</body>
</html>
