<?php
session_start();
include 'config.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Traitement du formulaire de création de CV
if (isset($_POST['submit_cv'])) {
    $id_user = $_SESSION['id_user'];
    $titre = $_POST['titre'];
    $presentation = $_POST['presentation'];

    $sql = "INSERT INTO cv (id_user, titre, presentation) VALUES ('$id_user', '$titre', '$presentation')";

    if (mysqli_query($conn, $sql)) {
        echo "CV créé avec succès !";
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Traitement du formulaire d'expérience
if (isset($_POST['submit_experience'])) {
    $id_cv = $_POST['id_cv'];
    $titre_poste = $_POST['titre_poste'];
    $entreprise = $_POST['entreprise'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $description = $_POST['description'];

    $sql = "INSERT INTO experiences (id_cv, titre_poste, entreprise, date_debut, date_fin, description) 
            VALUES ('$id_cv', '$titre_poste', '$entreprise', '$date_debut', '$date_fin', '$description')";

    if (mysqli_query($conn, $sql)) {
        echo "Expérience ajoutée avec succès !";
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Traitement du formulaire de formation
if (isset($_POST['submit_formation'])) {
    $id_cv = $_POST['id_cv'];
    $diplome = $_POST['diplome'];
    $ecole = $_POST['ecole'];
    $annee = $_POST['annee'];

    $sql = "INSERT INTO formations (id_cv, diplome, ecole, annee) 
            VALUES ('$id_cv', '$diplome', '$ecole', '$annee')";

    if (mysqli_query($conn, $sql)) {
        echo "Formation ajoutée avec succès !";
    } else {
        echo "Erreur : " . mysqli_error($conn);
    }
}

// Récupérer les CV de l'utilisateur
$user_id = $_SESSION['id_user'];
$cv_query = "SELECT * FROM cv WHERE id_user = $user_id";
$cv_result = mysqli_query($conn, $cv_query);

// Stocker tous les CV dans un tableau pour pouvoir les utiliser plusieurs fois
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

    // Ajouter au tableau
    $cv_full_data[] = [
        'cv' => $cv,
        'experiences' => $experiences,
        'formations' => $formations,
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-logo">MyCV</a><br>
            <ul class="navbar-menu">
                <li><a href="#" onclick="showSection('create-cv')">Créer CV</a></li>
                <li><a href="#" onclick="showSection('add-experience')">Ajouter Expérience</a></li>
                <li><a href="#" onclick="showSection('add-formation')">Ajouter Formation</a></li>
                <li><a href="#" onclick="showSection('my-cvs')">Mes CV</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <header class="header">
        <div class="container">
            <h1>Bienvenue, <?php echo $_SESSION['nom']; ?> 👋</h1>
            <p>Gérez facilement vos CV, expériences et formations.</p>
        </div>
    </header>
    <main class="container">
        <!-- Section par défaut : Mes CV -->

        <section id="my-cvs" class="dashboard-section">
    <h2>Mes CV</h2>

    <?php if (empty($cv_full_data)) { ?>
        <p>Vous n'avez pas encore créé de CV.</p>
    <?php } else { ?>
        <div class="cv-cards-container">
            <?php foreach ($cv_full_data as $data) { ?>
                <a href="cv_details.php?id_cv=<?php echo $data['cv']['id_cv']; ?>" class="cv-card-link">
                    <div class="cv-card">
                        <div class="cv-card-header">
                            <h3><?php echo htmlspecialchars($data['cv']['titre']); ?></h3>
                            <p class="cv-presentation"><?php echo nl2br(htmlspecialchars($data['cv']['presentation'])); ?></p>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
</section>

        <!-- Section : Créer un CV -->
        <section id="create-cv" class="dashboard-section" style="display:none;">
            <h2>Créer un nouveau CV</h2>
            <form method="POST" action="" class="form">
                <input type="text" name="titre" placeholder="Titre du CV" required>
                <textarea name="presentation" placeholder="Présentation" required></textarea>
                <button type="submit" name="submit_cv" class="btn-primary">Créer le CV</button>
            </form>
        </section>

        <!-- Section : Ajouter une expérience -->
        <section id="add-experience" class="dashboard-section" style="display:none;">
            <h2>Ajouter une expérience</h2>
            <form method="POST" action="" class="form">
                <select name="id_cv" required>
                    <?php foreach ($cv_list as $cv) { ?>
                        <option value="<?php echo $cv['id_cv']; ?>"><?php echo $cv['titre']; ?></option>
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

        <!-- Section : Ajouter une formation -->
        <section id="add-formation" class="dashboard-section" style="display:none;">
            <h2>Ajouter une formation</h2>
            <form method="POST" action="" class="form">
                <select name="id_cv" required>
                    <?php foreach ($cv_list as $cv) { ?>
                        <option value="<?php echo $cv['id_cv']; ?>"><?php echo $cv['titre']; ?></option>
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
