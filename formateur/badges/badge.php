<?php
session_start();
if (isset($_SESSION)) {
    extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../1-css/style.css">
    <link rel="stylesheet" href="badge.css">
    <style>
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #8a9bb8;
            font-size: 1rem;
            font-weight: 500;
            background: #f4f7fc;
            border-radius: 16px;
            border: 2px dashed #d0d8e4;
        }
        .page-title {
            display: flex;
            flex-direction: column;
            margin-bottom: 28px;
        }
        .page-title h1 {
            margin: 0;
        }
        .main-content {
            padding: 0 14px 100px 0;
        }
    </style>
    <title>Gestion des Badges - ISMO-SkillSwap</title>
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
                <div class="nav-item" onclick="location.href='../utilisateur/index.php'">
                    <span class="nav-icon">👥</span>
                    <span>Utilisateurs</span>
                </div>
                <div class="nav-item active" onclick="location.href='badge.php'">
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

        <main class="main-content">
            <div class="page-title">
                <h1>Gestion des badges</h1>
                <p class="eyebrow">Attribuez des badges aux stagiaires méritants</p>
            </div>

            <div class="badges-layout">
                <section class="badges-list-container">
                    <h2 class="panel-title">Badges disponibles</h2>
                    <?php
                    include("../../database/config.php");
                    $stmt = $db->query("SELECT * FROM badges");
                    $stmt->execute();
                    $badges = $stmt->fetchAll();
                    if (count($badges) == 0) {
                        echo '<div class="empty-state">Aucun badge disponible pour le moment.</div>';
                    }
                    ?>
                    <?php foreach ($badges as $badge): ?>
                    <div class="badge-item-row">
                        <div class="badge-icon-box"><?php echo $badge['icone']; ?></div>
                        <div class="badge-details">
                            <h3><?php echo $badge['nom']; ?></h3>
                            <p><?php echo $badge['points_requis']; ?> points requis</p>
                        </div>
                        <button class="btn-outline">Attribuer</button>
                    </div>
                    <?php endforeach; ?>
                </section>

                <aside class="badges-right-side">
                    <div class="action-panel">
                        <h2>Attribuer un badge</h2>
                        <div class="input-group">
                            <label>Badge à attribuer</label>
                            <select class="custom-select">
                                <option>Sélectionner un badge</option>
                                <option>🎯 Premier pas</option>
                                <option>⭐ Mentor actif</option>
                                <option>⚛️ Expert React</option>
                                <option>🤝 Collaborateur</option>
                                <option>🏆 Top contributeur</option>
                                <option>🔒 Sécurité Pro</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Stagiaire</label>
                            <input list="stagiaires-list" class="custom-search-input"
                                placeholder="Sélectionner un stagiaire">
                            <datalist id="stagiaires-list">
                                <option>Ahmed Idrissi-dev101</option>
                                <option>Sara El Amrani-dev201</option>
                            </datalist>
                        </div>
                        <button class="btn-primary-muted">Attribuer le badge</button>
                    </div>

                    <div class="history-panel">
                        <h2>Attributions récentes</h2>
                        <div class="history-item">
                            <div class="history-icon">🎯</div>
                            <div class="history-info">
                                <strong>Ahmed Idrissi</strong>
                                <span>Premier pas • 26/04/2026</span>
                            </div>
                        </div>
                        <div class="history-item">
                            <div class="history-icon">⭐</div>
                            <div class="history-info">
                                <strong>Sara El Amrani</strong>
                                <span>Mentor actif • 25/04/2026</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
    <script src="../../2-script/profile-menu.js"></script>
    <script src="badge.js"></script>
    <script src="../../2-script/search.js"></script>
</body>

</html>