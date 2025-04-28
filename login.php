<?php
session_start(); // Start the session
include 'config.php'; // Include database configuration
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}
// Check if the login form is submitted
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Find the user from the database
    $sql = "SELECT * FROM utilisateurs WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Successful authentication, create session variables
            $_SESSION['id_user'] = $user['id_user']; // User ID
            $_SESSION['nom'] = $user['nom']; // User name
            $_SESSION['email'] = $user['email']; // User email
            header("Location: dashboard.php"); // Redirect to the dashboard
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Email non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    <form method="POST" action="">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="mot_de_passe" required><br><br>

        <button type="submit" name="login">Se connecter</button>
    </form>
</body>
</html>
