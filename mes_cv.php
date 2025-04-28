<?php
session_start();
include 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
// Récupérer tous les CV de l'utilisateur
$id_user = $_SESSION['id_user'];
$sql_cv = "SELECT * FROM cv WHERE id_user = $id_user";
$result_cv = mysqli_query($conn, $sql_cv);

$cv_list = [];
while ($row = mysqli_fetch_assoc($result_cv)) {
    // Pour chaque CV, récupérer les expériences et formations
    $id_cv = $row['id_cv'];

    // Récupérer les expériences
    $sql_exp = "SELECT * FROM experiences WHERE id_cv = $id_cv";
    $result_exp = mysqli_query($conn, $sql_exp);
    $experiences = [];
    while ($exp = mysqli_fetch_assoc($result_exp)) {
        $experiences[] = $exp;
    }

    // Récupérer les formations
    $sql_form = "SELECT * FROM formations WHERE id_cv = $id_cv";
    $result_form = mysqli_query($conn, $sql_form);
    $formations = [];
    while ($form = mysqli_fetch_assoc($result_form)) {
        $formations[] = $form;
    }

    $cv_list[] = [
        'cv' => $row,
        'experiences' => $experiences,
        'formations' => $formations,
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes CV</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cv-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .cv-card h2 {
            color: #4D55CC;
            margin-bottom: 10px;
        }
        .cv-section {
            margin-top: 15px;
        }
        .cv-section h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .cv-section ul {
            list-style-type: none;
            padding: 0;
        }
        .cv-section ul li {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 5px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #4D55CC;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-btn">← Retour</a>
    <h1>Mes CVs</h1>

    <?php foreach ($cv_list as $item) { ?>
        <div class="cv-card">
            <h2><?php echo htmlspecialchars($item['cv']['titre']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($item['cv']['presentation'])); ?></p>

            <div class="cv-section">
                <h3>Expériences</h3>
                <ul>
                    <?php if (count($item['experiences']) > 0) {
                        foreach ($item['experiences'] as $exp) { ?>
                            <li>
                                <strong><?php echo htmlspecialchars($exp['titre_poste']); ?></strong> chez <?php echo htmlspecialchars($exp['entreprise']); ?> <br>
                                <small><?php echo htmlspecialchars($exp['date_debut']); ?> à <?php echo htmlspecialchars($exp['date_fin']); ?></small> <br>
                                <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                            </li>
                        <?php }
                    } else {
                        echo "<li>Aucune expérience ajoutée.</li>";
                    } ?>
                </ul>
            </div>

            <div class="cv-section">
                <h3>Formations</h3>
                <ul>
                    <?php if (count($item['formations']) > 0) {
                        foreach ($item['formations'] as $form) { ?>
                            <li>
                                <strong><?php echo htmlspecialchars($form['diplome']); ?></strong> à <?php echo htmlspecialchars($form['ecole']); ?> (<?php echo htmlspecialchars($form['annee']); ?>)
                            </li>
                        <?php }
                    } else {
                        echo "<li>Aucune formation ajoutée.</li>";
                    } ?>
                </ul>
            </div>
        </div>
    <?php } ?>

</div>

</body>
</html>
