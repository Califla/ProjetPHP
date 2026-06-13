
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Profil public</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="1-css/style.css" />
  <link rel="stylesheet" href="public_profile.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="sidebar-logo"><span>ISMO-SkillSwap</span></div>
      <nav class="sidebar-nav">
        <div class="nav-item" onclick="location.href='stagiaire/tableaubord/index.html'">
          <span class="nav-icon">🏠</span><span>Tableau de bord</span>
        </div>
        <div class="nav-item" onclick="location.href='stagiaire/mes%20demandes/index.html'">
          <span class="nav-icon">📋</span><span>Mes demandes</span>
        </div>
        <div class="nav-item" onclick="location.href='stagiaire/competences/index.html'">
          <span class="nav-icon">🏷️</span><span>Compétences</span>
        </div>
        <div class="nav-item" onclick="location.href='stagiaire/badges/index.html'">
          <span class="nav-icon">🎖️</span><span>Badges</span>
        </div>
        <div class="nav-item" onclick="location.href='stagiaire/passeport/index.html'">
          <span class="nav-icon">👤</span><span>Passeport</span>
        </div>
        <div class="nav-item" onclick="location.href='stagiaire/marketplace/index.html'">
          <span class="nav-icon">🛒</span><span>Marketplace</span>
        </div>
      </nav>
    </aside>

    <header class="header">
      <button class="pub-back" onclick="history.back()" style="margin-right:12px;">← Retour</button>
      <button class="notif-btn" aria-label="Notifications">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="notif-dot"></span>
      </button>
      <form class="header-search" action="#" onsubmit="return false;">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input id="globalSearch" class="header-search-input" type="search" placeholder="Rechercher un stagiaire...">
      </form>
      <div class="user-pill">
        <div class="user-info">
          <div class="user-name">Visiteur</div>
          <div class="user-role">Profil public</div>
        </div>
        <div class="user-avatar">?</div>
      </div>
    </header>

    <main class="main">
      <div class="pub-card" id="profileCard">
        <div class="pub-avatar" id="pubAvatar">?</div>
        <div class="pub-info">
          <h1 id="pubName">—</h1>
          <div class="pub-role" id="pubRole">—</div>
          <div class="pub-rating" id="pubRatingBlock">
            <span class="stars" id="pubStars"></span>
            <span class="score" id="pubScore"></span>
            <span class="count" id="pubCount"></span>
          </div>
        </div>
      </div>

      <div class="pub-stats">
        <div class="pub-stat">
          <div class="val" id="stat1">—</div>
          <div class="lbl" id="stat1Label">—</div>
        </div>
        <div class="pub-stat">
          <div class="val" id="stat2">—</div>
          <div class="lbl" id="stat2Label">—</div>
        </div>
        <div class="pub-stat">
          <div class="val" id="stat3">—</div>
          <div class="lbl" id="stat3Label">—</div>
        </div>
      </div>

      <div class="pub-grid" id="pubBottom">
    </main>
  </div>

  <script src="2-script/profile-menu.js"></script>
  <script src="2-script/search.js"></script>
  <script>
    const profiles = {
      "Youssef Benali": {
        role: "Mentor - Développement Digital",
        type: "mentor",
        initials: "YB",
        stat1: 12, stat1Label: "Aides effectuées",
        stat2: 5, stat2Label: "Compétences",
        stat3: 4, stat3Label: "Badges"
      },
      "Jean Martin": {
        role: "Stagiaire CYBERSEC",
        type: "stagiaire",
        initials: "JM",
        stat1: 3, stat1Label: "Aides reçues",
        stat2: 3, stat2Label: "Compétences",
        stat3: 2, stat3Label: "Badges"
      },
      "Sophie Bernard": {
        role: "Stagiaire DEV",
        type: "stagiaire",
        initials: "SB",
        stat1: 5, stat1Label: "Aides reçues",
        stat2: 4, stat2Label: "Compétences",
        stat3: 3, stat3Label: "Badges"
      },
      "Alex Chen": {
        role: "Stagiaire DATA",
        type: "stagiaire",
        initials: "AC",
        stat1: 2, stat1Label: "Aides reçues",
        stat2: 3, stat2Label: "Compétences",
        stat3: 1, stat3Label: "Badges"
      },
      "Thomas Lefevre": {
        role: "Stagiaire CYBERSEC",
        type: "stagiaire",
        initials: "TL",
        stat1: 4, stat1Label: "Aides reçues",
        stat2: 4, stat2Label: "Compétences",
        stat3: 2, stat3Label: "Badges"
      },
      "Ahmed Idrissi": {
        role: "Stagiaire DEV 101",
        type: "stagiaire",
        initials: "AI",
        stat1: 6, stat1Label: "Aides reçues",
        stat2: 5, stat2Label: "Compétences",
        stat3: 3, stat3Label: "Badges"
      },
      "Marie Dupont": {
        role: "Stagiaire DEV",
        type: "stagiaire",
        initials: "MD",
        stat1: 3, stat1Label: "Aides reçues",
        stat2: 3, stat2Label: "Compétences",
        stat3: 2, stat3Label: "Badges"
      },
      "Lafhal Jouariya": {
        role: "Formateur",
        type: "formateur",
        initials: "LJ",
        stat1: 24, stat1Label: "Stagiaires encadrés",
        stat2: 42, stat2Label: "Compétences validées",
        stat3: 18, stat3Label: "Badges attribués"
      }
    };

    const params = new URLSearchParams(window.location.search);
    const name = params.get('name') || 'Youssef Benali';
    const profile = profiles[name] || { role: 'Utilisateur', type: '', initials: '?', stat1: '—', stat1Label: '—', stat2: '—', stat2Label: '—', stat3: '—', stat3Label: '—' };

    document.getElementById('pubName').textContent = name;
    document.getElementById('pubAvatar').textContent = profile.initials;
    document.getElementById('pubRole').textContent = profile.role;
    document.getElementById('stat1').textContent = profile.stat1;
    document.getElementById('stat1Label').textContent = profile.stat1Label;
    document.getElementById('stat2').textContent = profile.stat2;
    document.getElementById('stat2Label').textContent = profile.stat2Label;
    document.getElementById('stat3').textContent = profile.stat3;
    document.getElementById('stat3Label').textContent = profile.stat3Label;

    const isMentor = profile.type === 'mentor';
    const ratingBlock = document.getElementById('pubRatingBlock');

    if (isMentor) {
      const ratings = JSON.parse(localStorage.getItem('mentorRatings') || '{}');
      const list = ratings[name];
      if (list && list.length > 0) {
        const sum = list.reduce((a, r) => a + r.rating, 0);
        const avg = (sum / list.length).toFixed(1);
        const full = Math.round(parseFloat(avg));
        document.getElementById('pubStars').textContent = '★'.repeat(full) + '☆'.repeat(5 - full);
        document.getElementById('pubScore').textContent = avg;
        document.getElementById('pubCount').textContent = '(' + list.length + ' avis)';
      } else {
        document.getElementById('pubStars').textContent = '☆☆☆☆☆';
        document.getElementById('pubScore').textContent = '—';
        document.getElementById('pubCount').textContent = '(aucun avis)';
      }
    } else {
      ratingBlock.style.display = 'none';
    }

    const bottomEl = document.getElementById('pubBottom');
    if (profile.type === 'formateur') {
      bottomEl.innerHTML = `
        <div class="pub-card-section">
          <div class="pub-card-title">Modules enseignés</div>
          <div class="comp-list">
            <div class="comp-item"><span>Développement Web</span><span class="comp-level">DEV 101</span></div>
            <div class="comp-item"><span>Algorithmique avancée</span><span class="comp-level">DEV 201</span></div>
            <div class="comp-item"><span>Cybersécurité</span><span class="comp-level">SEC 101</span></div>
            <div class="comp-item"><span>Base de données</span><span class="comp-level">DATA 101</span></div>
            <div class="comp-item"><span>Projet tutoré</span><span class="comp-level">DEV 301</span></div>
          </div>
        </div>
        <div class="pub-card-section">
          <div class="pub-card-title">Dernières validations</div>
          <div class="badge-list">
            <div class="badge-item">
              <div class="icon">✅</div>
              <div><div class="name">TypeScript</div><div class="date">Ahmed Idrissi - 26/04</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">✅</div>
              <div><div class="name">React Hooks</div><div class="date">Marie Dupont - 24/04</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">✅</div>
              <div><div class="name">Python avancé</div><div class="date">Sophie Bernard - 22/04</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">✅</div>
              <div><div class="name">Git & GitHub</div><div class="date">Jean Martin - 20/04</div></div>
            </div>
          </div>
        </div>`;
    } else {
      bottomEl.innerHTML = `
        <div class="pub-card-section">
          <div class="pub-card-title">Compétences validées</div>
          <div class="comp-list">
            <div class="comp-item"><span>React</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>JavaScript ES6+</span><span class="comp-level">Expert</span></div>
            <div class="comp-item"><span>Tailwind CSS</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>Git &amp; GitHub</span><span class="comp-level">Intermédiaire</span></div>
            <div class="comp-item"><span>Docker</span><span class="comp-level">Débutant</span></div>
          </div>
        </div>
        <div class="pub-card-section">
          <div class="pub-card-title">Badges obtenus</div>
          <div class="badge-list">
            <div class="badge-item">
              <div class="icon">🎯</div>
              <div><div class="name">Premier pas</div><div class="date">15/01/2026</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">⭐</div>
              <div><div class="name">Mentor actif</div><div class="date">20/02/2026</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">⚛️</div>
              <div><div class="name">Expert React</div><div class="date">10/03/2026</div></div>
            </div>
            <div class="badge-item">
              <div class="icon">🤝</div>
              <div><div class="name">Collaborateur</div><div class="date">01/04/2026</div></div>
            </div>
          </div>
        </div>`;
    }
  </script>
</body>
</html>
