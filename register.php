<?php
session_start(); // Start the session
include 'config.php'; // Include database configuration

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

// Vérifier si le formulaire est soumis
if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Sécuriser le mot de passe (hashage)
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà
    $check_email = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        echo "<div class='error'>Cet email est déjà utilisé. Veuillez en choisir un autre.</div>";
    } else {
        // Insérer dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom', '$email', '$mot_de_passe_hash')";

        if (mysqli_query($conn, $sql)) {
            echo "<div class='success'>Compte créé avec succès ! Vous pouvez maintenant vous connecter.</div>";
        } else {
            echo "<div class='error'>Erreur : " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="register.css">
</head>
<?php include 'navbar.php'; ?>

<body>
    <div class="signup-container">
        <h2>Créer un compte utilisateur</h2>
        <form method="POST" action="">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required placeholder="Entrez votre nom">

            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required placeholder="Entrez votre email">

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="Créez votre mot de passe">

            <button type="submit" name="submit">Créer mon compte</button>
        </form>
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>
</body>
</html>
