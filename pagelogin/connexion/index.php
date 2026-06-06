<?php
$msg  = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISMO-SkillSwap – Connexion</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="connexion.css" />
  <script src="../js/toggle-password.js" defer></script>
</head>

<body>

  <aside class="left">
    <div class="jsp">
      <div class="brand">
        <h1>ISMO-SkillSwap</h1>
        <p>Plateforme d'échange de compétences ISMO Tétouan</p>
      </div>
    </div>
    <div class="features">
      <h2>Pourquoi SkillSwap ?</h2>
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Valorisez vos compétences et progressez ensemble</span>
      </div>
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Obtenez de l'aide de vos pairs en temps réel</span>
      </div>

      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Gagnez des badges et des points de reconnaissance</span>
      </div>

      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <span class="feature-text">Construisez votre passeport de compétences</span>
      </div>
    </div>

    <p class="footer-text">© 2026 ISMO Tétouan – Institut Spécialisé dans les métiers de l'offshoring</p>
  </aside>

  <main class="right">
    <div class="card">
      <?php if ($msg): ?>
            <div id="toastMsg" class="toast success"><?php echo htmlspecialchars($msg); ?></div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div id="toastMsg" class="toast error"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
        </form>
      <h2>Connexion</h2>
      <p class="subtitle">Accédez à votre espace personnel</p>

      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="votre.email@ismo.ma" autocomplete="email" />
      </div>

      <div class="field">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <label for="password">Mot de passe</label>
          <div class="password-wrapper">
            <input type="password" id="password" placeholder="••••••••" autocomplete="current-password" />
            <button type="button" class="toggle-pw" onclick="togglePassword('password', this)" aria-label="Afficher le mot de passe">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
      </div>

      <div class="row-options">
        <label class="remember">
          <input type="checkbox" id="remember" />
          <span>Se souvenir de moi</span>
        </label>
        <a href="#" class="forgot">Mot de passe oublié ?</a>
      </div>

      <button class="btn-primary">Se connecter</button>

      <p class="register">Pas encore de compte ? <a href="../inscription/inscription.php">S'inscrire</a></p>

    </div>
    </form>
  </main>
  <div class="help-btn">?</div>

  <script>
    function dismissToast() {
      var t = document.getElementById('toastMsg');
      if (t) {
        t.classList.add('toast-hide');
        setTimeout(function() {
          t.remove();
        }, 400);
      }
    }
    <?php if ($msg || $error): ?>
      setTimeout(dismissToast, 4500);
    <?php endif; ?>
  </script>

</body>

</html>
