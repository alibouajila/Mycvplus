<?php
session_start();
include 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: home.php");
    exit();
}
// Notification de succès
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    echo "<div class='notification show' id='notif'>$message</div>";
    unset($_SESSION['success_message']);
}

// Traitement du formulaire de création de CV
if (isset($_POST['submit_cv'])) {
    $id_user = $_SESSION['id_user'];
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $presentation = mysqli_real_escape_string($conn, $_POST['presentation']);

    $sql = "INSERT INTO cv (id_user, titre, presentation) VALUES ('$id_user', '$titre', '$presentation')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "CV créé avec succès !";
        header("Location: dashboard.php#add-experience");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Traitement du formulaire d'expérience
if (isset($_POST['submit_experience'])) {
    $id_cv = $_POST['id_cv'];
    $titre_poste = mysqli_real_escape_string($conn, $_POST['titre_poste']);
    $entreprise = mysqli_real_escape_string($conn, $_POST['entreprise']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Validation des dates
    if ($date_fin && $date_debut > $date_fin) {
        $_SESSION['success_message'] = "La date de fin ne peut pas être antérieure à la date de début.";
        header("Location: dashboard.php#add-experience");
        exit();
    }

    $sql = "INSERT INTO experiences (id_cv, titre_poste, entreprise, date_debut, date_fin, description) 
            VALUES ('$id_cv', '$titre_poste', '$entreprise', '$date_debut', '$date_fin', '$description')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Expérience ajoutée avec succès !";
        header("Location: dashboard.php#add-formation");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Traitement du formulaire de formation
if (isset($_POST['submit_formation'])) {
    $id_cv = $_POST['id_cv'];
    $diplome = mysqli_real_escape_string($conn, $_POST['diplome']);
    $ecole = mysqli_real_escape_string($conn, $_POST['ecole']);
    $annee = $_POST['annee'];

    $sql = "INSERT INTO formations (id_cv, diplome, ecole, annee) 
            VALUES ('$id_cv', '$diplome', '$ecole', '$annee')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Formation ajoutée avec succès !";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Récupérer les CV de l'utilisateur
$user_id = $_SESSION['id_user'];
$cv_query = "SELECT * FROM cv WHERE id_user = $user_id";
$cv_result = mysqli_query($conn, $cv_query);

// Stocker tous les CV
$cv_list = [];
while ($cv = mysqli_fetch_assoc($cv_result)) {
    $cv_list[] = $cv;
}

// Récupérer tous les CV avec leurs expériences et formations
$cv_full_data = [];
foreach ($cv_list as $cv) {
    $id_cv = $cv['id_cv'];

    // Récupérer expériences
    $experience_query = "SELECT * FROM experiences WHERE id_cv = $id_cv";
    $experience_result = mysqli_query($conn, $experience_query);
    $experiences = [];
    while ($exp = mysqli_fetch_assoc($experience_result)) {
        $experiences[] = $exp;
    }

    // Récupérer formations
    $formation_query = "SELECT * FROM formations WHERE id_cv = $id_cv";
    $formation_result = mysqli_query($conn, $formation_query);
    $formations = [];
    while ($form = mysqli_fetch_assoc($formation_result)) {
        $formations[] = $form;
    }

    // Ajouter tout
    $cv_full_data[] = [
        'cv' => $cv,
        'experiences' => $experiences,
        'formations' => $formations,
    ];
}

// Suppression de CV
if (isset($_POST['delete_cv'])) {
    $delete_cv_id = $_POST['delete_cv_id'];

    mysqli_query($conn, "DELETE FROM experiences WHERE id_cv = '$delete_cv_id'");
    mysqli_query($conn, "DELETE FROM formations WHERE id_cv = '$delete_cv_id'");

    if (mysqli_query($conn, "DELETE FROM cv WHERE id_cv = '$delete_cv_id'")) {
        $_SESSION['success_message'] = "CV supprimé avec succès !";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.dashboard-section').forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const notif = document.getElementById('notif');
            if (notif) {
                setTimeout(() => notif.classList.remove('show'), 1000);
            }
        });
    </script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <header class="header">
        <br><br><br>
        <div class="container">
            <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?> 👋</h1>
            <p>Gérez facilement vos CV, expériences et formations.</p>
        </div>
    </header>

    <main class="container">
        <!-- Mes CV -->
        <section id="my-cvs" class="dashboard-section">
            <h2 style="text-align: center;">Mes CV</h2>

            <?php if (empty($cv_full_data)) { ?>
                <p style="text-align: center; margin-top: 100px;">Vous n'avez pas encore créé de CV.</p>
            <?php } else { ?>
                <div class="cv-cards-container">
                    <?php foreach ($cv_full_data as $data) { ?>
                        <a href="cv_details.php?id_cv=<?php echo $data['cv']['id_cv']; ?>" class="cv-card-link">
                            <div class="cv-card">
                                <div class="cv-card-header">
                                    <h3><?php echo htmlspecialchars($data['cv']['titre']); ?></h3>
                                    <p class="cv-presentation"><?php echo nl2br(htmlspecialchars($data['cv']['presentation'])); ?></p>
                                </div>
                                <div class="cv-card-footer">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_cv_id" value="<?php echo $data['cv']['id_cv']; ?>">
                                        <button type="submit" name="delete_cv" class="btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

        <!-- Créer un CV -->
        <section id="create-cv" class="dashboard-section" style="display:none;">
            <h2>Créer un nouveau CV</h2>
            <form method="POST" class="form">
                <input type="text" name="titre" placeholder="Titre du CV" required>
                <textarea name="presentation" placeholder="Présentation" required></textarea>
                <button type="submit" name="submit_cv" class="btn-primary">Créer le CV</button>
            </form>
        </section>

        <!-- Ajouter une expérience -->
        <section id="add-experience" class="dashboard-section" style="display:none;">
            <h2>Ajouter une expérience</h2>
            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="error-message"><?php echo $_SESSION['error_message']; ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php } ?>
            <form method="POST" class="form">
                <select name="id_cv" required>
                    <?php foreach ($cv_list as $cv) { ?>
                        <option value="<?php echo $cv['id_cv']; ?>"><?php echo htmlspecialchars($cv['titre']); ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="titre_poste" placeholder="Titre du poste" required>
                <input type="text" name="entreprise" placeholder="Entreprise" required>
                <input type="date" name="date_debut" required>
                <input type="date" name="date_fin">
                <textarea name="description" placeholder="Description" required></textarea>
                <button type="submit" name="submit_experience" class="btn-primary">Ajouter</button>
            </form>
        </section>

        <!-- Ajouter une formation -->
        <section id="add-formation" class="dashboard-section" style="display:none;">
            <h2>Ajouter une formation</h2>
            <form method="POST" class="form">
                <select name="id_cv" required>
                    <?php foreach ($cv_list as $cv) { ?>
                        <option value="<?php echo $cv['id_cv']; ?>"><?php echo htmlspecialchars($cv['titre']); ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="diplome" placeholder="Diplôme" required>
                <input type="text" name="ecole" placeholder="École" required>
                <input type="number" name="annee" placeholder="Année" required>
                <button type="submit" name="submit_formation" class="btn-primary">Ajouter</button>
            </form>
        </section>
    </main>
</body>
</html>
