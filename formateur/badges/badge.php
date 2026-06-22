<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'formateur') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
if (isset($_SESSION)) {
    extract($_SESSION);
}
include("../../database/config.php");
try{
$badges = $db->query("SELECT * FROM badges")->fetchAll(PDO::FETCH_ASSOC);
$stagiaires = $db->query("SELECT id_user, nom, prenom, filiere FROM utilisateurs WHERE role = 'stagiaire' OR role = 'mentor' ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$historique = $db->query("SELECT ob.date_obtention, u.nom, u.prenom, b.icone, b.nom AS badge_nom
    FROM obtention_badges ob
    JOIN utilisateurs u ON ob.id_user = u.id_user
    JOIN badges b ON ob.id_badge = b.id_badge
    ORDER BY ob.date_obtention DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    echo "Erreur: " . $e->getMessage();
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
            <form class="header-search" action="../../search.php" method="GET">
                <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input name="q" class="header-search-input" type="search"
                    placeholder="Rechercher un stagiaire...">
            </form>
            <div class="user-pill" data-email="<?php echo $email; ?>">
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
            <?php if (isset($_GET['msg'])): ?>
            <div class="toast success">✓ Badge attribué avec succès</div>
            <?php elseif (isset($_GET['error'])): ?>
            <div class="toast error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <div class="page-title">
                <h1>Gestion des badges</h1>
                <p class="eyebrow">Attribuez des badges aux stagiaires méritants</p>
            </div>

            <div class="badges-layout">
                <section class="badges-list-container">
                    <h2 class="panel-title">Badges disponibles</h2>
                    <?php if (count($badges) == 0): ?>
                        <div class="empty-state">Aucun badge disponible pour le moment.</div>
                    <?php endif; ?>
                    <?php foreach ($badges as $badge): ?>
                    <div class="badge-item-row" data-id-badge="<?= $badge['id_badge'] ?>">
                        <div class="badge-icon-box"><?= htmlspecialchars($badge['icone']) ?></div>
                        <div class="badge-details">
                            <h3><?= htmlspecialchars($badge['nom']) ?></h3>
                            <p><?= $badge['points_requis'] ?> points requis</p>
                        </div>
                        <button type="button" class="btn-outline">Attribuer</button>
                    </div>
                    <?php endforeach; ?>
                </section>

                <aside class="badges-right-side">
                    <form class="action-panel" method="POST" action="attribuer_badge.php">
                        <h2>Attribuer un badge</h2>
                        <div class="input-group">
                            <label>Badge à attribuer</label>
                            <select name="id_badge" class="custom-select" required>
                                <option value="">-- Sélectionner un badge --</option>
                                <?php foreach ($badges as $b): ?>
                                <option value="<?= $b['id_badge'] ?>"><?= htmlspecialchars($b['icone'] . ' ' . $b['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Stagiaire</label>
                            <input list="stagiaires-list" id="stagiaire-input" name="stagiaire" class="custom-search-input"
                                placeholder="Sélectionner un stagiaire" autocomplete="off" required>
                            <datalist id="stagiaires-list">
                                <?php foreach ($stagiaires as $s): ?>
                                <option value="<?= htmlspecialchars($s['nom'] . ' ' . $s['prenom'] . ' - ' . $s['filiere']) ?>" data-id="<?= $s['id_user'] ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" name="id_user" id="id_user_hidden">
                        </div>
                        <button type="submit" class="btn-primary-muted">Attribuer le badge</button>
                    </form>

                    <div class="history-panel">
                        <h2>Attributions récentes</h2>
                        <?php if (count($historique) == 0): ?>
                            <div class="history-item"><p style="color:#8a9bb8">Aucune attribution pour le moment</p></div>
                        <?php else: ?>
                        <?php foreach ($historique as $h): ?>
                        <div class="history-item">
                            <div class="history-icon"><?= htmlspecialchars($h['icone']) ?></div>
                            <div class="history-info">
                                <strong><?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?></strong>
                                <span><?= htmlspecialchars($h['badge_nom']) ?> • <?= date('d/m/Y', strtotime($h['date_obtention'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </main>
    </div>
    <script src="../../2-script/profile-menu.js"></script>
    <script src="badge.js"></script>
    
</body>

</html>