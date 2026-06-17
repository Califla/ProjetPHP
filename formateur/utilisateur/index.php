<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Utilisateurs</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="stylesheet" href="utilisateur.css" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/tableaubord.php'">
          <span class="nav-icon">🏠</span>
          <span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='../validecompetence/validecompetence.php'">
          <span class="nav-icon">📋</span>
          <span>Valider compétences</span>
        </div>
        <div class="nav-item" onclick="location.href='../Competences/compe.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <div class="nav-item active" onclick="location.href='index.php'">
          <span class="nav-icon">👥</span>
          <span>Utilisateurs</span>
        </div>
        <div class="nav-item" onclick="location.href='../badges/badge.php'">
          <span class="nav-icon">🎖️</span>
          <span>Badges</span>
        </div>
        <div class="nav-item" onclick="location.href='../marketplace/marketplace.php'">
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
      <div class="user-pill" data-email="jouariya@ismo.ma">
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
    </header>

    <main class="main">
      <section class="content-area">
        <div class="page-header">
          <div>
            <p class="eyebrow">Gestion des utilisateurs</p>
            <h1>Gérez les rôles des stagiaires</h1>
          </div>
        </div>

        <div class="stats-grid">
          <article class="stat-card">
            <span class="stat-label">Total utilisateurs</span>
            <strong>260</strong>
          </article>
          <article class="stat-card">
            <span class="stat-label">Stagiaires</span>
            <strong>187</strong>
          </article>
          <article class="stat-card">
            <span class="stat-label">Mentors</span>
            <strong>45</strong>
          </article>
          <article class="stat-card">
            <span class="stat-label">En attente</span>
            <strong>8</strong>
          </article>
        </div>

        <section class="table-card">
          <div class="table-header">
            <div class="search-wrapper">
              <span class="search-icon">🔍</span>
              <input type="search" placeholder="Rechercher par nom, email..." aria-label="Recherche utilisateurs" />
            </div>
            <div class="filter-row">
              <label>
                <span class="visually-hidden">Filtrer par rôle</span>
                <select>
                  <option>Tous les rôles</option>
                  <option>Stagiaire</option>
                  <option>Mentor</option>
                </select>
              </label>
              <label>
                <span class="visually-hidden">Filtrer par statut</span>
                <select>
                  <option>Tous les statuts</option>
                  <option>Actif</option>
                  <option>En attente</option>
                  <option>Suspendu</option>
                </select>
              </label>
            </div>
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>Utilisateur</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Filière</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="user-cell">
                    <div class="user-cell-inner">
                      <span class="user-avatar">Ah</span>
                      <div>
                        <strong>Ahmed Idrissi</strong>
                      </div>
                    </div>
                  </td>
                  <td>ahmed.idrissi@ismo.ma</td>
                  <td><span class="badge badge-stagiaire">Stagiaire</span></td>
                  <td>DEV 101</td>
                  <td><span class="status status-success">Actif</span></td>
                  <td class="actions-cell">
                    <div class="actions-cell-inner">
                      <button class="btn-role" data-role="stagiaire">Passer Mentor</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>