<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
if (isset($_SESSION)){
    extract($_SESSION);
}
try {
    include('../../database/config.php');

    $total_aides = $db->query("SELECT COUNT(*) FROM aide")->fetchColumn();
    $resolues = $db->query("SELECT COUNT(*) FROM aide WHERE status = 'resolu'")->fetchColumn();
    $taux_resolution = $total_aides > 0 ? round(($resolues / $total_aides) * 100) : 0;
    $total_formateurs = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'formateur'")->fetchColumn();
    $actifs = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE statut = 'actif'")->fetchColumn();

    $req_comp = $db->prepare("SELECT c.nom, COUNT(v.id_competence) AS total
        FROM validation_competence v
        JOIN competences c ON v.id_competence = c.id_competence
        GROUP BY v.id_competence ORDER BY total DESC LIMIT 6");
    $req_comp->execute();
    $competences = $req_comp->fetchAll(PDO::FETCH_ASSOC);
    $max_comp = count($competences) > 0 ? max(array_column($competences, 'total')) : 1;
    $bar_colors = ['#2e6fca', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

    $req_filiere = $db->prepare("SELECT filiere, COUNT(*) AS total FROM utilisateurs WHERE filiere IS NOT NULL AND filiere != '' GROUP BY filiere");
    $req_filiere->execute();
    $filieres = $req_filiere->fetchAll(PDO::FETCH_ASSOC);
    $total_filieres = array_sum(array_column($filieres, 'total'));
    $filiere_colors = ['#2e6fca', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#e11d48'];

    $req_evo = $db->prepare("SELECT DATE_FORMAT(date_pub, '%Y-%m') AS mois, COUNT(*) AS total
        FROM aide WHERE date_pub >= NOW() - INTERVAL 12 MONTH
        GROUP BY mois ORDER BY mois ASC");
    $req_evo->execute();
    $evolutions = $req_evo->fetchAll(PDO::FETCH_ASSOC);
    $max_evo = count($evolutions) > 0 ? max(array_column($evolutions, 'total')) : 1;

    $req_mentors = $db->prepare("SELECT u.id_user, u.nom, u.prenom, COUNT(ae.id_proposition) AS nb_aides, ROUND(AVG(ae.note_mentor), 1) AS note_moyenne
        FROM aide_effectuee ae
        JOIN utilisateurs u ON ae.id_mentor = u.id_user
        GROUP BY u.id_user ORDER BY nb_aides DESC LIMIT 5");
    $req_mentors->execute();
    $mentors = $req_mentors->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Statistiques</title>
  <link rel="stylesheet" href="stat.css" />
  <link rel="stylesheet" href="../../1-css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='../tableaubord/table.php'">
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
        <div class="nav-item active" onclick="location.href='stat.php'">
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
          <div class="user-role"><?php echo $role ?></div>
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

    <main class="content-area">
      <div class="page-header">
        <div>
          <h1>Statistiques</h1>
          <p class="page-subtitle">Analyse de l'activité de la plateforme</p>
        </div>
      </div>

      <div class="stats-kpi">
        <article class="kpi-card">
          <div class="kpi-icon">📈</div>
          <div class="kpi-content">
            <span class="kpi-label">Total aides</span>
            <div class="kpi-value"><?= $total_aides ?></div>
          </div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon">🎯</div>
          <div class="kpi-content">
            <span class="kpi-label">Taux de résolution</span>
            <div class="kpi-value"><?= $taux_resolution ?>%</div>
          </div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon">⭐</div>
          <div class="kpi-content">
            <span class="kpi-label">Formateurs</span>
            <div class="kpi-value"><?= $total_formateurs ?></div>
          </div>
        </article>
        <article class="kpi-card">
          <div class="kpi-icon">👥</div>
          <div class="kpi-content">
            <span class="kpi-label">Utilisateurs actifs</span>
            <div class="kpi-value"><?= $actifs ?></div>
          </div>
        </article>
      </div>

      <div class="charts-grid">
        <section class="chart-section">
          <h2>Compétences les plus demandées</h2>
          <div class="chart-container bar-chart">
            <svg viewBox="0 0 <?= count($competences) * 80 + 80 ?> 350" xmlns="http://www.w3.org/2000/svg">
              <?php
                $bar_w = 40;
                $gap = 80;
                $start_x = 60;
                $chart_h = 300;
                $base_y = 330;
                if (empty($competences)){
                  echo "Aucune donnée pour le moment";
                }
                foreach ($competences as $i => $comp):
                  $bar_h = round(($comp['total'] / $max_comp) * 250);
                  $x = $start_x + $i * $gap;
                  $color = $bar_colors[$i % count($bar_colors)];
              ?>
              <rect x="<?= $x ?>" y="<?= $base_y - $bar_h ?>" width="<?= $bar_w ?>" height="<?= $bar_h ?>" fill="<?= $color ?>" rx="4" />
              <text x="<?= $x + $bar_w / 2 ?>" y="<?= $base_y + 15 ?>" text-anchor="middle" class="axis-label"><?= htmlspecialchars($comp['nom']) ?></text>
              <text x="<?= $x + $bar_w / 2 ?>" y="<?= $base_y - $bar_h - 8 ?>" text-anchor="middle" class="axis-label" fill="<?= $color ?>"><?= $comp['total'] ?></text>
              <?php endforeach; ?>
            </svg>
          </div>
        </section>

        <section class="chart-section">
          <h2>Activité par filière</h2>
          <div class="chart-container pie-chart">
            <svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">
              <?php
                $circ = 2 * pi() * 80;
                $offset = 0;
                foreach ($filieres as $i => $f):
                  $pct = $total_filieres > 0 ? $f['total'] / $total_filieres : 0;
                  $dash = $pct * $circ;
                  $color = $filiere_colors[$i % count($filiere_colors)];
                  $label_pct = round($pct * 100);
              ?>
              <circle cx="150" cy="150" r="80" fill="none" stroke="<?= $color ?>" stroke-width="50" stroke-dasharray="<?= $dash ?> <?= $circ ?>" stroke-dashoffset="<?= -$offset ?>" />
              <?php $offset += $dash; ?>
              <?php endforeach; ?>
              <?php if ($total_filieres > 0): ?>
              <text x="150" y="150" text-anchor="middle" dominant-baseline="central" class="pie-label" style="font-size:18px;font-weight:800"><?= $total_filieres ?></text>
              <?php endif; ?>
            </svg>
            <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:10px;justify-content:center">
              <?php foreach ($filieres as $i => $f):
                $pct = $total_filieres > 0 ? round(($f['total'] / $total_filieres) * 100) : 0;
              ?>
              <span style="display:flex;align-items:center;gap:4px;font-size:0.8rem;color:#6c7a94">
                <span style="width:10px;height:10px;border-radius:50%;background:<?= $filiere_colors[$i % count($filiere_colors)] ?>"></span>
                <?= htmlspecialchars($f['filiere']) ?> (<?= $pct ?>%)
              </span>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      </div>

      <div class="charts-grid full-width">
        <section class="chart-section">
          <h2>Évolution mensuelle des aides</h2>
          <div class="chart-container line-chart">
            <?php if (count($evolutions) == 0): ?>
              <p style="color:#6c7a94;text-align:center">Aucune donnée pour le moment</p>
            <?php else: ?>
            <svg viewBox="0 0 800 250" xmlns="http://www.w3.org/2000/svg">
              <?php
                $nb_points = count($evolutions);
                $chart_w = 700;
                $pad_left = 60;
                $step_x = $nb_points > 1 ? $chart_w / ($nb_points - 1) : 0;
                $y_base = 200;
                $y_top = 30;
                $y_range = $y_base - $y_top;

                $points = [];
                foreach ($evolutions as $i => $e):
                  $x = $pad_left + ($nb_points > 1 ? $i * $step_x : $chart_w / 2);
                  $y = $y_base - ($max_evo > 0 ? ($e['total'] / $max_evo) * $y_range : 0);
                  $points[] = "$x,$y";
                endforeach;
              ?>
              <line x1="<?= $pad_left ?>" y1="<?= $y_top ?>" x2="<?= $pad_left ?>" y2="<?= $y_base ?>" stroke="#ddd" stroke-width="1" />
              <line x1="<?= $pad_left ?>" y1="<?= $y_base ?>" x2="<?= $pad_left + $chart_w ?>" y2="<?= $y_base ?>" stroke="#ddd" stroke-width="1" />
              <text x="<?= $pad_left - 10 ?>" y="<?= $y_base + 4 ?>" class="axis-label" text-anchor="end">0</text>
              <text x="<?= $pad_left - 10 ?>" y="<?= $y_top + 4 ?>" class="axis-label" text-anchor="end"><?= $max_evo ?></text>

              <?php if ($nb_points > 1): ?>
              <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="#2e6fca" stroke-width="3" />
              <?php endif; ?>

              <?php foreach ($evolutions as $i => $e):
                $x = $pad_left + ($nb_points > 1 ? $i * $step_x : $chart_w / 2);
                $y = $y_base - ($max_evo > 0 ? ($e['total'] / $max_evo) * $y_range : 0);
                $mois_label = substr($e['mois'], 5, 2) . '/' . substr($e['mois'], 2, 2);
              ?>
              <circle cx="<?= $x ?>" cy="<?= $y ?>" r="5" fill="#2e6fca" />
              <text x="<?= $x ?>" y="<?= $y_base + 20 ?>" text-anchor="middle" class="axis-label"><?= $mois_label ?></text>
              <?php endforeach; ?>
            </svg>
            <?php endif; ?>
          </div>
        </section>
      </div>

      <section class="top-mentors">
        <h2>Top Mentors</h2>
        <div class="mentors-list">
          <?php if (count($mentors) == 0): ?>
            <div class="mentor-item"><p style="color:#6c7a94">Aucun mentor pour le moment</p></div>
          <?php else: ?>
          <?php foreach ($mentors as $i => $m):
            $initial = strtoupper(substr($m['nom'] ?? '?', 0, 1) . substr($m['prenom'] ?? '?', 0, 1));
          ?>
          <article class="mentor-item">
            <div class="mentor-rank">#<?= $i + 1 ?></div>
            <div class="mentor-avatar"><?= $initial ?></div>
            <div class="mentor-info">
              <div class="mentor-name"><?= htmlspecialchars($m['nom'] . ' ' . $m['prenom']) ?></div>
              <div class="mentor-aides"><?= $m['nb_aides'] ?> aides</div>
            </div>
            <div class="mentor-rating">
              <span class="star">⭐</span>
              <span class="rating"><?= $m['note_moyenne'] ?></span>
            </div>
          </article>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>
  <script src="../../2-script/profile-menu.js"></script>
  
</body>

</html>