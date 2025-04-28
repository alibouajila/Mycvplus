<?php
session_start();
include 'config.php';

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}
// Initialize message variables
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['success_message'] = "Connexion réussite ! Bienvenue.";
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Mot de passe incorrect.";
            $message_type = "error";
        }
    } else {
        $message = "Email non trouvé.";
        $message_type = "error";
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

<?php if (!empty($message)): ?>
  <div id="notification" class="notification <?php echo $message_type; ?>">
    <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 900); // 
    }
});
</script>

</body>
</html>
