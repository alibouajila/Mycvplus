<?php
session_start();
include 'config.php';

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

// Initialize empty message
$message = '';
$message_type = '';

if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        $message = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        $message_type = "error";
    } else {
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom', '$email', '$mot_de_passe_hash')";
        if (mysqli_query($conn, $sql)) {
            $message = "Compte créé avec succès ! Redirection vers la page de connexion...";
            $message_type = "success";
            // Set a flag to trigger redirection after success
            $redirect = true;
        } else {
            $message = "Erreur lors de l'inscription : " . mysqli_error($conn);
            $message_type = "error";
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
<body>

<?php include 'navbar.php'; ?>

<?php if (!empty($message)): ?>
  <div id="notification" class="notification <?php echo $message_type; ?>">
    <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

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

<script>
window.addEventListener('DOMContentLoaded', () => {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.classList.add('show');
        // Hide notification after 2 second
        setTimeout(() => {
            notification.classList.remove('show');
        }, 2000);
    }

    <?php if (!empty($redirect) && $redirect): ?>
        // Redirect after 2 seconds
        setTimeout(() => {
            window.location.href = "login.php";
        }, 2000);
    <?php endif; ?>
});
</script>

</body>
</html>
