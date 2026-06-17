<?php
session_start();
if (isset($_SESSION)){
    extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Tableau de bord Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="table.css" />
  <link rel="stylesheet" href="../../1-css/style.css" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item active" onclick="location.href='../Tableau de bord/table.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../Utulisateurs/utili.php'">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </div>
        <div class="nav-item" onclick="location.href='../Badges/badges.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item" onclick="location.href='../Moderation/mode.php'">
          <span class="nav-icon">🛡️</span>
          <span>Modération</span>
        </div>
        <div class="nav-item" onclick="location.href='../Statistiques/stat.php'">
          <span class="nav-icon">📊</span>
          <span>Statistiques</span>
        </div>
        <div class="nav-item" onclick="location.href='../Marketplace/market.php'">
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
      <div class="user-pill" data-email="admin@ismo.ma">
        <div class="user-info">
          <div class="user-name"><?php echo $nom . " " . $prenom; ?></div>
          <div class="user-role"><?php echo $role ?></div>
        </div>
        <?php
        if ($photo) {
          echo '<img class="user-avatar" src="../../pagelogin/photo/' . $photo . '" alt="Photo de profil">';
        } else {
          echo '<div class="user-avatar">' . substr($nom, 0, 1) . substr($prenom, 0, 1) . '</div>';
        }
        ?>
    </header>

    <main class="content-area">
      <section class="hero-section">
        <h1>Tableau de bord Admin</h1>
        <p>Vue d'ensemble de la plateforme</p>
      </section>

      <section class="stats-grid">
        <article class="stat-card">
          <div class="stat-head">
            <strong>Utilisateurs inscrits</strong>
            <span class="stat-icon icon-users"></span>
          </div>
          <div class="stat-number">260</div>
          <div class="stat-note"><span>+12</span> ce mois</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <strong>Utilisateurs actifs</strong>
            <span class="stat-icon icon-active"></span>
          </div>
          <div class="stat-number">187</div>
          <div class="stat-note">&nbsp;</div>
        </article>
        <article class="stat-card">
          <div class="stat-head">
            <strong>Comptes suspendus</strong>
            <span class="stat-icon icon-suspend"></span>
          </div>
          <div class="stat-number">5</div>
          <div class="stat-note">&nbsp;</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <strong>Publications signalées</strong>
            <span class="stat-icon icon-flag"></span>
          </div>
          <div class="stat-number">5</div>
          <div class="stat-note">&nbsp;</div>
        </article>
      </section>

      <section class="alert-panel">
        <div class="alert-header">
          <span class="alert-label">Alertes</span>
          <span class="alert-badge">!</span>
        </div>
        <ul class="alert-list">
          <li>5 publications signalées en attente de modération</li>
          <li>15 nouvelles demandes de compte à approuver</li>
        </ul>
      </section>

      <section class="bottom-cards">
        <article class="chart-card">
          <div class="card-title">Répartition par rôle</div>
          <div class="donut-chart" aria-hidden="true"></div>
          <div class="chart-legend">
            <div class="legend-item">
              <span class="legend-color legend-blue"></span>
              <span>Stagiaires</span>
            </div>
            <div class="legend-item">
              <span class="legend-color legend-green"></span>
              <span>Formateurs</span>
            </div>
            <div class="legend-item">
              <span class="legend-color legend-orange"></span>
              <span>Admins</span>
            </div>
          </div>
        </article>

        <article class="activity-card">
          <div class="card-title">Activité récente</div>
          <div class="activity-item">
            <p><strong>Ahmed Idrissi</strong> a publié une demande d'aide</p>
            <span>il y a 5 min</span>
          </div>
          <div class="activity-item">
            <p><strong>Sara El Amrani</strong> a validé une compétence React</p>
            <span>il y a 12 min</span>
          </div>
          <div class="activity-item">
            <p><strong>Prof. Benjelloun</strong> a attribué un badge</p>
            <span>il y a 1 h</span>
          </div>
          <div class="activity-item">
            <p><strong>Youssef Benali</strong> s'est inscrit</p>
            <span>il y a 2 h</span>
          </div>
        </article>
      </section>
    </main>
  </div>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>