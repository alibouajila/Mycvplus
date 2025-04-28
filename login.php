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
            echo "<div class='error'>Mot de passe incorrect.</div>";
        }
    } else {
        echo "<div class='error'>Email non trouvé.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<?php include 'navbar.php'; ?>

    <div class="login-container">
        <h2>Connexion</h2>
        <form method="POST" action="">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required placeholder="Entrez votre email">

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="Entrez votre mot de passe">

            <button type="submit" name="login">Se connecter</button>
        </form>
        <p>Pas encore inscrit? <a href="register.php">Créer un compte</a></p>
    </div>
</body>
</html>
