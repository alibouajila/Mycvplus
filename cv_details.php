<?php
session_start();
include 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Récupérer l'id du CV passé dans l'URL
if (isset($_GET['id_cv'])) {
    $id_cv = $_GET['id_cv'];
} else {
    echo "CV introuvable.";
    exit();
}

// Récupérer les détails du CV
$sql = "SELECT * FROM cv WHERE id_cv = $id_cv AND id_user = {$_SESSION['id_user']}";
$result = mysqli_query($conn, $sql);
$cv = mysqli_fetch_assoc($result);

if (!$cv) {
    echo "CV introuvable ou vous n'êtes pas autorisé à le voir.";
    exit();
}
// Récupérer les expériences et formations liées au CV
$experiences_query = "SELECT * FROM experiences WHERE id_cv = $id_cv";
$experiences_result = mysqli_query($conn, $experiences_query);
$experiences = [];
while ($exp = mysqli_fetch_assoc($experiences_result)) {
    $experiences[] = $exp;
}

$formations_query = "SELECT * FROM formations WHERE id_cv = $id_cv";
$formations_result = mysqli_query($conn, $formations_query);
$formations = [];
while ($form = mysqli_fetch_assoc($formations_result)) {
    $formations[] = $form;
}
$photo = !empty($cv['photo']) ? 'uploads/' . htmlspecialchars($cv['photo']) : 'uploads/profile.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du CV</title>
    <link rel="stylesheet" href="cv_details.css">
    <link rel="stylesheet" href="nav.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        function printCV() {
            const printContent = document.getElementById('cvContent');
            const newWindow = window.open('', '', 'height=600,width=800');
            newWindow.document.write('<html><head><title>Impression CV</title>');
            newWindow.document.write('<link rel="stylesheet" href="cv_details.css" type="text/css">');
            newWindow.document.write('</head><body>');
            newWindow.document.write(printContent.innerHTML);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="dashboard.php" class="navbar-logo">Mycvplus</a><br>
        <ul class="navbar-menu">
            <li><a href="dashboard.php">Mes CV</a></li>
            <?php if (isset($_SESSION['id_user'])): ?>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<br><br><br>

<div class="cvdetails" id="cvContent">
<header style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Détails du CV: <?php echo htmlspecialchars($cv['titre']); ?></h1>
    <div class="cv-photo">
        <img src="<?php echo $photo; ?>" alt="Photo de profil" class="profile-photo">
    </div>
</header>


    <section>
        <h2>Présentation</h2>
        <p><?php echo nl2br(htmlspecialchars($cv['presentation'])); ?></p>
    </section>

    <section>
        <h2>Expériences</h2>
        <?php if (empty($experiences)) { ?>
            <p>Aucune expérience ajoutée.</p>
        <?php } else { ?>
            <ul>
                <?php foreach ($experiences as $exp) { ?>
                    <li>
                        <strong><?php echo htmlspecialchars($exp['titre_poste']); ?></strong> chez <?php echo htmlspecialchars($exp['entreprise']); ?>
                        <br>
                        <small><?php echo $exp['date_debut']; ?> - <?php echo $exp['date_fin'] ?: 'Présent'; ?></small>
                        <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </section>

    <section>
        <h2>Formations</h2>
        <?php if (empty($formations)) { ?>
            <p>Aucune formation ajoutée.</p>
        <?php } else { ?>
            <ul>
                <?php foreach ($formations as $form) { ?>
                    <li>
                        <?php echo htmlspecialchars($form['diplome']); ?> - <?php echo htmlspecialchars($form['ecole']); ?> (<?php echo $form['annee']; ?>)
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </section>

    <div style="text-align:center">
        <button class="print-btn" onclick="printCV()">Imprimer le CV</button>
    </div>
</div>

</body>
</html>
