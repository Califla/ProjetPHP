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
  <title>ISMO-SkillSwap – Mes compétences</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="compe.css">
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
        <div class="nav-item active" onclick="location.href='compe.php'">
          <span class="nav-icon">🏷️</span>
          <span>Compétences</span>
        </div>
        <div class="nav-item" onclick="location.href='../utilisateur/index.php'">
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
        <div class="user-avatar"><?php echo substr($nom, 0, 1).substr($prenom, 0, 1); ?></div>
      </div>
    </header>

    <main class="main">
      <section class="content-area">
        <div class="page-header">
          <div>
            <h1>Gestion des compétences</h1>
          </div>
          <a href="#" class="btn btn-primary">+ Attribuer une compétence</a>
        </div>

        <div class="search-panel">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="search" placeholder="Rechercher une compétence..." aria-label="Recherche compétence" />
          </div>
          <div class="select-box">
            <select aria-label="Toutes les catégories">
              <option>Toutes les catégories</option>
              <option>Frontend</option>
              <option>Backend</option>
              <option>DevOps</option>
              <option>Cybersécurité</option>
              <option>Base de données</option>
            </select>
          </div>
        </div>

        <div class="category-grid">
          <article class="overview-card">
            <strong>12</strong>
            <span>Frontend</span>
          </article>
          <article class="overview-card">
            <strong>8</strong>
            <span>Backend</span>
          </article>
          <article class="overview-card">
            <strong>6</strong>
            <span>DevOps</span>
          </article>
          <article class="overview-card">
            <strong>5</strong>
            <span>Cybersécurité</span>
          </article>
          <article class="overview-card">
            <strong>7</strong>
            <span>Base de données</span>
          </article>
        </div>

        <div class="cards-grid">
          <article class="skill-card">
            <div class="skill-card-header">
              <h2>React</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier React">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer React">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">Frontend</span>
            <div class="skill-row"><span>Déclarations</span><strong>45</strong></div>
            <div class="skill-row"><span>Validations</span><strong>38</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>84%</strong>
            </div>
          </article>

          <article class="skill-card">
            <div class="skill-card-header">
              <h2>Python</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier Python">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer Python">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">Langages</span>
            <div class="skill-row"><span>Déclarations</span><strong>52</strong></div>
            <div class="skill-row"><span>Validations</span><strong>48</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>92%</strong>
            </div>
          </article>

          <article class="skill-card">
            <div class="skill-card-header">
              <h2>Docker</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier Docker">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer Docker">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">DevOps</span>
            <div class="skill-row"><span>Déclarations</span><strong>28</strong></div>
            <div class="skill-row"><span>Validations</span><strong>22</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>79%</strong>
            </div>
          </article>

          <article class="skill-card">
            <div class="skill-card-header">
              <h2>Penetration Testing</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier Penetration Testing">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer Penetration Testing">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">Cybersécurité</span>
            <div class="skill-row"><span>Déclarations</span><strong>15</strong></div>
            <div class="skill-row"><span>Validations</span><strong>12</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>80%</strong>
            </div>
          </article>

          <article class="skill-card">
            <div class="skill-card-header">
              <h2>MongoDB</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier MongoDB">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer MongoDB">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">Base de données</span>
            <div class="skill-row"><span>Déclarations</span><strong>32</strong></div>
            <div class="skill-row"><span>Validations</span><strong>28</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>88%</strong>
            </div>
          </article>

          <article class="skill-card">
            <div class="skill-card-header">
              <h2>Kubernetes</h2>
              <div class="skill-card-actions">
                <button class="icon-btn" aria-label="Modifier Kubernetes">✏️</button>
                <button class="icon-btn danger" aria-label="Supprimer Kubernetes">🗑️</button>
              </div>
            </div>
            <span class="skill-badge">DevOps</span>
            <div class="skill-row"><span>Déclarations</span><strong>12</strong></div>
            <div class="skill-row"><span>Validations</span><strong>8</strong></div>
            <div class="skill-row progress-row">
              <span>Taux validation</span>
              <strong>67%</strong>
            </div>
          </article>
        </div>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>
