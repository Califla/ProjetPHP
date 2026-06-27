<?php
session_start();
if (isset($_SESSION)) {
  extract($_SESSION);
  if ($role !== 'stagiaire' && $role !== 'mentor') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
  }
}
try {
  include('../../database/config.php');
  $stmtScore = $db->prepare("SELECT score FROM utilisateurs WHERE id_user = ?");
  $stmtScore->execute([$id_user]);
  $_SESSION['score'] = $stmtScore->fetchColumn();

  $aidesDonnees = $db->prepare("SELECT COUNT(*) FROM aide_effectuee WHERE id_mentor = ?");
  $aidesDonnees->execute([$id_user]);
  $aidesDonnees = $aidesDonnees->fetchColumn();

  $competencesVal = $db->prepare("SELECT COUNT(*) FROM validation_competence WHERE id_user = ? AND status = 'validee'");
  $competencesVal->execute([$id_user]);
  $competencesVal = $competencesVal->fetchColumn();

  $noteMoy = $db->prepare("SELECT note_moyenne FROM utilisateurs WHERE id_user = ?");
  $noteMoy->execute([$id_user]);
  $noteMoy = $noteMoy->fetchColumn();

  $mesBadges = $db->prepare("SELECT b.*, ob.date_obtention FROM obtention_badges ob JOIN badges b ON b.id_badge = ob.id_badge WHERE ob.id_user = ? ORDER BY ob.date_obtention DESC");
  $mesBadges->execute([$id_user]);
  $mesBadges = $mesBadges->fetchAll(PDO::FETCH_ASSOC);

  $topMentors = $db->query("SELECT u.id_user, u.nom, u.prenom, u.photo, COUNT(ae.id_proposition) AS nb_aides, COALESCE(ROUND(AVG(ae.note_mentor), 1), 0) AS note FROM aide_effectuee ae JOIN utilisateurs u ON ae.id_mentor = u.id_user WHERE u.statut = 'actif' GROUP BY u.id_user ORDER BY nb_aides DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

  $demandesRecentes = $db->prepare("SELECT id_demande, titre, status, date_pub FROM aide WHERE id_user = ? ORDER BY date_pub DESC LIMIT 5");
  $demandesRecentes->execute([$id_user]);
  $demandesRecentes = $demandesRecentes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $aidesDonnees = 0;
  $competencesVal = 0;
  $noteMoy = 0;
  $mesBadges = [];
  $topMentors = [];
  $demandesRecentes = [];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Tableau de bord</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../1-css/style.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <div class="layout">

    <aside class="sidebar">
      <div class="sidebar-logo">
        <span>ISMO-SkillSwap</span>
      </div>
      <nav class="sidebar-nav">

        <div class="nav-item active" onclick="location.href='index.php'">
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

        <div class="nav-item" onclick="location.href='../passeport/index.php'">
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
      <form class="header-search" action="../../search.php" method="GET">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input name="q" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
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
    <div class="page-title">Tableau de bord</div>
    <div class="page-sub">Vue d'ensemble de vos activités</div>


    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Aides données</span>
          <svg class="stat-icon" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="4" />
            <path d="M6 20v-2a6 6 0 0 1 12 0v2" />
          </svg>
        </div>
        <div class="stat-value"><?php echo $aidesDonnees; ?></div>
      </div>


      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Compétences</span>
          <svg class="stat-icon" viewBox="0 0 24 24">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            <polyline points="17 6 23 6 23 12" />
          </svg>
        </div>
        <div class="stat-value"><?php echo $competencesVal; ?></div>

      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Points / Rang</span>
          <svg class="stat-icon" viewBox="0 0 24 24">
            <polyline points="8 6 2 12 8 18" />
            <polyline points="16 6 22 12 16 18" />
          </svg>
        </div>
        <div class="stat-value"><?php echo $_SESSION['score']; ?></div>

      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Note moyenne</span>
          <svg class="stat-icon" viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
          </svg>
        </div>
        <div class="stat-value"><?php echo $noteMoy; ?></div>
      </div>
    </div>

    <div class="bottom-grid">

      <div class="card">
        <div class="card-title">Mes Badges</div>
        <?php if (count($mesBadges) == 0): ?>
          <div style="font-size:0.85rem; color:var(--gray-text); text-align:center; padding:20px 0;">
            Aucun badge confirmé pour le moment.<br />Votre formateur vous en attribuera.
          </div>
        <?php else: ?>
          <div class="badge-list">
            <?php foreach ($mesBadges as $b): ?>
              <div class="badge-item" title="Obtenu le <?php echo $b['date_obtention']; ?>">
                <span class="badge-emoji"><?php echo $b['icone']; ?></span>
                <div style="flex:1">
                  <div style="font-weight:600"><?php echo $b['nom']; ?></div>
                  <?php
                  $date = new DateTime($b['date_obtention']);
                  $date_formatee = $date->format('d/m/Y');
                  ?>
                   <div style="font-size:0.75rem;color:var(--gray-text)">Obtenu le <?php echo htmlspecialchars($date_formatee); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>


      <div class="card">
        <div class="card-title">Top Mentors</div>
        <?php if (count($topMentors) == 0): ?>
          <div style="font-size:0.85rem; color:var(--gray-text); text-align:center; padding:20px 0;">
            Aucun mentor pour le moment.
          </div>
        <?php else: ?>
          <div class="mentor-list">
            <?php $i = 1;
            $colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444']; ?>
            <?php foreach ($topMentors as $m): ?>
              <div class="mentor-item">
                <span class="mentor-rank">#<?php echo $i; ?></span>
                <div class="mentor-avatar" style="background:<?php echo $colors[($i - 1) % 5]; ?>"><?php echo substr($m['prenom'], 0, 1) . substr($m['nom'], 0, 1); ?></div>
                <div class="mentor-info">
                  <div class="mentor-name"><?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></div>
                  <div class="mentor-aides"><?php echo $m['nb_aides']; ?> aide<?php echo $m['nb_aides'] > 1 ? 's' : ''; ?></div>
                </div>
                <div class="mentor-rating">
                  <svg class="star-icon" viewBox="0 0 24 24">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                  </svg>
                  <?php echo $m['note']; ?>
                </div>
              </div>
            <?php $i++; endforeach; ?>
          </div>
        <?php endif; ?>
      </div>


      <div class="card">
        <div class="card-title">Demandes récentes</div>
        <?php if (count($demandesRecentes) == 0): ?>
          <div style="font-size:0.85rem; color:var(--gray-text); text-align:center; padding:20px 0;">
            Aucune demande pour le moment.
          </div>
        <?php else: ?>
          <div class="demande-list">
            <?php foreach ($demandesRecentes as $d):
              $now = new DateTime();
              $dt = new DateTime($d['date_pub']);
              $diff = $now->getTimestamp() - $dt->getTimestamp();
              if ($diff < 60) $timeAgo = "il y a $diff s";
              elseif ($diff < 3600) $timeAgo = "il y a " . floor($diff / 60) . " min";
              elseif ($diff < 86400) $timeAgo = "il y a " . floor($diff / 3600) . " h";
              elseif ($diff < 604800) $timeAgo = "il y a " . floor($diff / 86400) . " j";
              else $timeAgo = "il y a " . floor($diff / 604800) . " sem";
              $statusClass = match ($d['status']) {
                'resolu' => 'resolu',
                'en_coure' => 'en_coure',
                default => 'ouvert'
              };
            ?>
              <div class="demande-item">
                <div class="demande-row">
                  <span class="demande-title"><?php echo htmlspecialchars($d['titre']); ?></span>
                  <span class="badge-status <?php echo $statusClass; ?>"><?php echo ucfirst($d['status']); ?></span>
                </div>
                <div class="demande-time"><?php echo $timeAgo; ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  </div>

  <div class="help-btn">?</div>

  <script>
    function setPage(el, page) {
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      el.classList.add('active');
    }
  </script>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>