<?php
session_start(); // Start the session
include 'config.php'; // on connecte à la base
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
        echo "Cet email est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        // Insérer dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom', '$email', '$mot_de_passe_hash')";

        if (mysqli_query($conn, $sql)) {
            echo "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
        } else {
            echo "Erreur : " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
</head>
<body>
    <h2>Créer un compte utilisateur</h2>
    <form method="POST" action="">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="mot_de_passe" required><br><br>

        <button type="submit" name="submit">Créer mon compte</button>
    </form>
</body>
</html>
