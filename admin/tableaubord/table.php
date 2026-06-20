<?php
session_start();
if (isset($_SESSION)){
    extract($_SESSION);
}
function timeAgo($date) {
    if (!$date) return '';
    $now = new DateTime();
    $dt = new DateTime($date);
    $diff = $now->getTimestamp() - $dt->getTimestamp();
    if ($diff < 60) return "il y a $diff s";
    if ($diff < 3600) return "il y a " . floor($diff / 60) . " min";
    if ($diff < 86400) return "il y a " . floor($diff / 3600) . " h";
    if ($diff < 604800) return "il y a " . floor($diff / 86400) . " j";
    return "il y a " . floor($diff / 604800) . " sem";
}

$activities = [];
try{
    include('../../database/config.php');
    $utiinscrit = $db->query("SELECT COUNT(*) AS total FROM utilisateurs")->fetchColumn();
    $utiinscritce_mois = $db->query("SELECT COUNT(*) AS total FROM utilisateurs WHERE date_inscription >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)")->fetchColumn();
    $utiactif = $db->query("SELECT COUNT(*) AS total FROM utilisateurs WHERE statut = 'actif'")->fetchColumn();
    $utisuspendu = $db->query("SELECT COUNT(*) AS total FROM utilisateurs WHERE statut = 'suspendu'")->fetchColumn();
    $pubsignal = $db->query("SELECT COUNT(*) AS total FROM aide WHERE `signal` > 0")->fetchColumn();
    $utienattente = $db->query("SELECT COUNT(*) AS total FROM utilisateurs WHERE statut = 'en_attente'")->fetchColumn();


    $req_act = $db->prepare("
        (SELECT 'inscription' AS type, u.id_user, u.date_inscription AS date_action, CONCAT(u.nom, ' ', u.prenom) AS user_name, NULL AS details1, NULL AS details2 FROM utilisateurs u WHERE u.date_inscription >= NOW() - INTERVAL 7 DAY)
        UNION ALL
        (SELECT 'publication' AS type, a.id_user, a.date_pub AS date_action, CONCAT(u.nom, ' ', u.prenom) AS user_name, a.titre AS details1, NULL AS details2 FROM aide a JOIN utilisateurs u ON a.id_user = u.id_user WHERE a.date_pub >= NOW() - INTERVAL 7 DAY)
        UNION ALL
        (SELECT 'proposition' AS type, p.id_user, p.date_prop AS date_action, CONCAT(u.nom, ' ', u.prenom) AS user_name, a.titre AS details1, NULL AS details2 FROM propositions_aide p JOIN utilisateurs u ON p.id_user = u.id_user JOIN aide a ON p.id_demande = a.id_demande WHERE p.date_prop >= NOW() - INTERVAL 7 DAY)
        UNION ALL
        (SELECT 'badge' AS type, o.id_user, o.date_obtention AS date_action, CONCAT(u.nom, ' ', u.prenom) AS user_name, b.nom AS details1, NULL AS details2 FROM obtention_badges o JOIN utilisateurs u ON o.id_user = u.id_user JOIN badges b ON o.id_badge = b.id_badge WHERE o.date_obtention >= NOW() - INTERVAL 7 DAY)
        UNION ALL
        (SELECT 'validation' AS type, v.id_user, v.date_validation AS date_action, CONCAT(u.nom, ' ', u.prenom) AS user_name, c.nom AS details1, v.niveau AS details2 FROM validation_competence v JOIN utilisateurs u ON v.id_user = u.id_user JOIN competences c ON v.id_competence = c.id_competence WHERE v.date_validation >= NOW() - INTERVAL 7 DAY AND v.status = 'validee')
        ORDER BY date_action DESC
    ");
    $req_act->execute();
    $activities = $req_act->fetchAll(PDO::FETCH_ASSOC);
    # Répartition par rôle
    $req_roles = $db->query("SELECT role, COUNT(*) AS total FROM utilisateurs GROUP BY role")->fetchAll(PDO::FETCH_ASSOC);
    $total_users = array_sum(array_column($req_roles, 'total'));
    $deg = 0;
    $gradient_parts = [];
    $colors = ['stagiaire' => '#3b82f6', 'formateur' => '#16a34a', 'mentor' => '#f59e0b', 'admin' => '#8b5cf6'];
    $labels = ['stagiaire' => 'Stagiaires', 'formateur' => 'Formateurs', 'mentor' => 'Mentors', 'admin' => 'Admins'];
    foreach ($req_roles as $r) {
        $angle = $total_users > 0 ? round(($r['total'] / $total_users) * 360) : 0;
        if ($angle > 0) {
            $gradient_parts[] = $colors[$r['role']] . " {$deg}deg " . ($deg + $angle) . "deg";
            $deg += $angle;
        }
    }
    if ($deg < 360) {
        $gradient_parts[] = '#dbe4f4 ' . $deg . 'deg 360deg';
    }
    $conic_gradient = 'conic-gradient(' . implode(',', $gradient_parts) . ')';
}catch(PDOException $e){
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
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
          <div class="stat-number"><?php echo $utiinscrit; ?></div>
          <div class="stat-note"><span>+<?php echo $utiinscritce_mois; ?></span> ce mois</div>
        </article>

        <article class="stat-card">
          <div class="stat-head">
            <strong>Utilisateurs actifs</strong>
            <span class="stat-icon icon-active"></span>
          </div>
          <div class="stat-number"><?php echo $utiactif; ?></div>
          <div class="stat-note">&nbsp;</div>
        </article>
        <article class="stat-card">
          <div class="stat-head">
            <strong>Comptes suspendus</strong>
            <span class="stat-icon icon-suspend"></span>
          </div>
          <div class="stat-number"><?php echo $utisuspendu; ?></div>
          <div class="stat-note">&nbsp;</div>
        </article>
        <?php if ($pubsignal > 0): ?>
        <article class="stat-card">
          <div class="stat-head">
            <strong>Publications signalées</strong>
            <span class="stat-icon icon-flag"></span>
          </div>
          <div class="stat-number"><?php echo $pubsignal; ?></div>
          <div class="stat-note">&nbsp;</div>
        </article>
        <?php endif; ?>
      </section>
      <?php if ($pubsignal > 0 || $utienattente > 0): ?>
      <section class="alert-panel">
        <div class="alert-header">
          <span class="alert-label">Alertes</span>
          <span class="alert-badge">!</span>
        </div>
        <ul class="alert-list">
          <?php if ($pubsignal > 0): ?>
          <li><?php echo $pubsignal; ?> publications signalées en attente de modération</li>
          <?php endif; ?>
          <?php if ($utienattente > 0): ?>
          <li><?php echo $utienattente; ?> nouvelles demandes de compte à approuver</li>
          <?php endif; ?>
        </ul>
      </section>
      <?php endif; ?>
      <section class="bottom-cards">
        <article class="chart-card">
          <div class="card-title">Répartition par rôle</div>
          <div class="donut-chart" style="background:<?= $conic_gradient ?>" aria-hidden="true"></div>
          <div class="chart-legend">
            <?php foreach ($req_roles as $r):
              $pct = $total_users > 0 ? round(($r['total'] / $total_users) * 100) : 0;
            ?>
            <div class="legend-item">
              <span class="legend-color" style="background:<?= $colors[$r['role']] ?>"></span>
              <span><?= $labels[$r['role']] ?> (<?= $pct ?>%)</span>
            </div>
            <?php endforeach; ?>
          </div>
        </article>

        <article class="activity-card">
          <div class="card-title">Activité récente</div>
          <?php if (count($activities) == 0): ?>
            <div class="activity-item"><p>Aucune activité récente</p></div>
          <?php else: ?>
          <?php foreach ($activities as $act):
            $msg = match ($act['type']) {
              'inscription' => "s'est inscrit",
              'publication' => 'a publié une demande d\'aide : ' . htmlspecialchars($act['details1']),
              'proposition' => 'a proposé son aide sur : ' . htmlspecialchars($act['details1']),
              'badge'       => 'a obtenu le badge ' . htmlspecialchars($act['details1']),
              'validation'  => 'a validé la compétence ' . htmlspecialchars($act['details1']),
              default       => 'a fait une action'
            };
          ?>
          <div class="activity-item">
            <p><strong><?= htmlspecialchars($act['user_name']) ?></strong> <?= $msg ?></p>
            <span><?= timeAgo($act['date_action']) ?></span>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </article>
      </section>
    </main>
  </div>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  <script src="../../2-script/search.js"></script>
</body>

</html>