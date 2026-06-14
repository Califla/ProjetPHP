<?php
session_start();
if (isset($_SESSION)){
    extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../1-css/style.css">
    <link rel="stylesheet" href="validecompetence.css">
    <title>Validation des compétences | ISMO-SkillSwap</title>
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
                <div class="nav-item active" onclick="location.href='validecompetence.php'">
                    <span class="nav-icon">📋</span>
                    <span>Valider compétences</span>
                </div>
                <div class="nav-item" onclick="location.href='../Competences/compe.php'">
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
                <input id="globalSearch" class="header-search-input" type="search"
                    placeholder="Rechercher un stagiaire...">
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
            <div class="page-title">
                <h1>Validation des compétences</h1>
                <p class="eyebrow">Validez ou refusez les déclarations de compétences des stagiaires</p>
            </div>

            <div class="search-section">
                <div class="search-wrapper">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                    <input type="text" placeholder="Rechercher par stagiaire ou compétence...">
                </div>
                <select class="filiere-select">
                    <option>Toutes les filières</option>
                    <option>DEV </option>
                    <option>infrastructures</option>
                </select>
            </div>

            <div class="stats-row">
                <div class="stat-card-v">
                    <div class="stat-v-num">5</div>
                    <div class="stat-v-label">En attente de validation</div>
                </div>
                <div class="stat-card-v">
                    <div class="stat-v-num">127</div>
                    <div class="stat-v-label">Validées ce mois</div>
                </div>
                <div class="stat-card-v red-stat">
                    <div class="stat-v-num">8</div>
                    <div class="stat-v-label">Refusées ce mois</div>
                </div>
            </div>

            <div class="validation-container">
                <div class="v-card-item">
                    <div class="v-card-header">
                        <div class="v-avatar">Ah</div>
                        <div class="v-user-details">
                            <div class="v-skill-line">
                                <span class="v-skill-name">TypeScript</span>
                                <span class="v-level-tag">Intermédiaire</span>
                            </div>
                            <div class="v-student-name">Ahmed Idrissi • DEV 101</div>
                        </div>
                    </div>

                    <div class="v-justification">
                        <strong>Justification:</strong> J'ai suivi le cours TypeScript et complété 3 projets personnels
                        avec cette technologie.
                    </div>

                    <div class="v-card-footer">
                        <span class="v-date">Déclaré le 26/04/2026</span>
                        <div class="v-buttons">
                            <button class="v-btn-refuse">✕ Refuser</button>
                            <button class="v-btn-validate">✓ Valider</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
  <script src="validecompetence.js"></script>
</body>

</html>

</html>
