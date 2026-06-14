<?php
session_start();
if (isset($_SESSION)) {
    extract($_SESSION);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../1-css/style.css">
    <link rel="stylesheet" href="tableaubord.css">
    <title>Document</title>

</head>

<body>
    <div class="layout">
        <aside class="sidebars">
            <aside class="sidebar">
                <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
                <nav class="sidebar-nav">
                    <div class="nav-item active" onclick="location.href='index.php'">
                        <span class="nav-icon">🏠</span>
                        <span>Tableau de bord</span>
                    </div>
                    <div class="nav-item " onclick="location.href='../validecompetence/validecompetence.php'">
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
                <h1 class="texte-title">Tableau de bord Formateur</h1>
                <p class="eyebrow">Vue d'ensemble de l'activité des stagiaires</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Stagiaires actifs</h3>
                    <div class="val">85</div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                </div>
                <div class="stat-card">
                    <h3>Compétences en attente</h3>
                    <div class="val">12</div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
                <div class="stat-card">
                    <h3>Aides échangées</h3>
                    <div class="val">234 <span class="trend">+18 cette semaine</span></div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    </svg>
                </div>
                <div class="stat-card">
                    <h3>Note moyenne</h3>
                    <div class="val">4.5</div>
                    <svg class="card-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
            </div>

            <div class="panels-grid">
                <div class="panel">
                    <div class="panel-h">
                        <h2>Compétences à valider</h2><a href="#"
                            style="color:var(--accent); text-decoration:none; font-size:13px; font-weight:600;">Voir
                            tout</a>
                    </div>
                    <div class="item-box">
                        <div class="item-top"><span style="font-weight:600;">TypeScript</span> <span
                                class="tag">Intermédiaire</span></div>
                        <span style="color:#94a3b8; font-size:13px;">Ahmed Idrissi</span>
                        <div class="btn-group">
                            <button class="btn-action" style="background:#22c55e;">✓</button>
                            <button class="btn-action" style="background:#ef4444;">✕</button>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-h">
                        <h2>Top Stagiaires</h2>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div
                                    style="width:34px; height:34px; border-radius:50%; background:var(--accent); color:white; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">
                                    AI</div>
                                <div>
                                    <div style="font-size:14px; font-weight:600;">Ahmed Idrissi</div>
                                    <div style="font-size:11px; color:#94a3b8;">DEV 101</div>
                                </div>
                            </div>
                            <span style="color:var(--accent); font-weight:700;">450pts</span>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-h">
                        <h2>Compétences demandées</h2>
                    </div>
                    <div class="chart-container">
                        <div class="chart-row">
                            <div class="chart-label">React</div>
                            <div class="bar-area">
                                <div class="bar-fill" style="width: 80%;"></div>
                            </div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label">Python</div>
                            <div class="bar-area">
                                <div class="bar-fill" style="width: 65%;"></div>
                            </div>
                        </div>
                        <div class="axis-x"><span>0</span><span>15</span><span>30</span><span>45</span><span>60</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../2-script/profile-menu.js"></script>
    <script src="../../2-script/search.js"></script>
    <script src="tableaubord.js"></script>
</body>

</html>

</html>