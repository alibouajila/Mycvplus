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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du CV</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-logo">MyCV</a>
            <ul class="navbar-menu">
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <header class="header">
        <div class="container">
            <h1>Détails du CV: <?php echo htmlspecialchars($cv['titre']); ?></h1>
        </div>
    </header>

    <main class="container">
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
    </main>
</body>
</html>
