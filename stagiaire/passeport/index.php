<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  if ($role !== 'stagiaire' && $role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Passeport</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/index.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../mes demandes/index.php'">
          <span class="nav-icon">📋</span>
          <span>Mes demandes</span>
        </div>
        <div class="nav-item" onclick="location.href='../competences/index.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <?php if ($role === 'mentor'):?>
        <div class="nav-item" onclick="location.href='../mes propositions/index.php'">
          <span class="nav-icon">🤝</span>
          <span>Mes propositions</span>
        </div>
        <?php endif; ?>
        <div class="nav-item" onclick="location.href='../badges/index.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item active" onclick="location.href='index.php'">
          <span class="nav-icon">👤</span>
          <span>Passeport</span>
        </div>
        <div class="nav-item" onclick="location.href='../marketplace/index.php'">
          <span class="nav-icon">🛒</span>
          <span>Marketplace</span>
        </div>
      </nav>
    </aside>

    <header class="header">
      <form class="header-search" action="#" onsubmit="return false;">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input id="globalSearch" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
      </form>
      <div class="user-pill" data-email="<?php echo $email; ?>">
        <div class="user-info">

          <div class="user-name"><?php echo $nom . " " . $prenom; ?></div>
          <div class="user-role"><?php echo $role; ?></div>
        </div>
        <?php
        if ($photo) {
          echo '<img class="user-avatar" src="../../pagelogin/photo/' . $photo . '" alt="Photo de profil">';
        } else {
          echo '<div class="user-avatar">' . substr($nom, 0, 1) . substr($prenom, 0, 1) . '</div>';
        }
        ?>
      </div>
    </header>

    <main class="main">
      <section class="passport-top">
        <div>
          <h1 class="page-title">Votre profil complet </h1>
          <p class="page-label">Mon passeport de compétences est exportable pour valoriser vos acquis</p>
        </div>
        <button class="export-btn">Exporter en PDF</button>
      </section>

      <section class="passport-summary">
        <div class="profile-card">
          <div class="profile-badge">AI</div>
          <div class="profile-info">
          <div class="profile-name">Ahmed Idrissi</div>
          <div class="profile-role">Stagiaire DEV 101 - Développement Digital</div>
          </div>
        </div>
        <div class="summary-stats">
          <div class="stat-block">
            <div class="stat-label">Points</div>
            <div class="stat-value">450</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Rang</div>
            <div class="stat-value">#12</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Aides</div>
            <div class="stat-value">12</div>
          </div>
        </div>
      </section>

      <section class="passport-grid">
        <article class="card competencies-card">
          <div class="card-title">Compétences validées</div>
          <div class="competence-list">
            <div class="comp-item"><span>React</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>JavaScript ES6+</span><span class="comp-level">Expert</span></div>
            <div class="comp-item"><span>Tailwind CSS</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>Git &amp; GitHub</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>Docker</span><span class="comp-level">Débutant</span></div>
          </div>
        </article>

        <article class="card badges-card">
          <div class="card-title">Badges obtenus</div>
          <div class="badge-grid">
            <div class="badge-card">
              <div class="badge-icon">🎯</div>
              <div>
                <div class="badge-name">Premier pas</div>
                <div class="badge-date">15/01/2026</div>
              </div>
            </div>
            <div class="badge-card">
              <div class="badge-icon">⭐</div>
              <div>
                <div class="badge-name">Mentor actif</div>
                <div class="badge-date">20/02/2026</div>
              </div>
            </div>
            <div class="badge-card">
              <div class="badge-icon">⚛️</div>
              <div>
                <div class="badge-name">Expert React</div>
                <div class="badge-date">10/03/2026</div>
              </div>
            </div>
            <div class="badge-card">
              <div class="badge-icon">🤝</div>
              <div>
                <div class="badge-name">Collaborateur</div>
                <div class="badge-date">01/04/2026</div>
              </div>
            </div>
          </div>
        </article>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>