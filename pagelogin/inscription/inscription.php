<?php
$err = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $err = [];
        if (!isset($role) || $role == "") $err["role"] = "le rôle est obligatoire";
        if (!isset($nom) || $nom == "") $err["nom"] = "le nom est obligatoire";
        else if(!preg_match("/^[a-zA-Z]+$/", $nom)) $err["nom"] = "le nom ne doit contenir que des lettres";
        if (!isset($prenom) || $prenom == "") $err["prenom"] = "le prénom est obligatoire";
        else if(!preg_match("/^[a-zA-Z]+$/", $prenom)) $err["prenom"] = "le prénom ne doit contenir que des lettres";
        if (!isset($email) || $email == "") $err["email"] = "l'email est obligatoire";
        else if (!preg_match("/^[a-zA-Z0-9._%+-]+@ismo\.ma$/", $email)) $err["email"] = "l'email doit être au format ismo.ma";
        if (strtolower($role) == "stagiaire" && (!isset($filiere) || $filiere == "")) $err["filiere"] = "la filière est obligatoire";
        if (!isset($password) || $password == "") $err["password"] = "le mot de passe est obligatoire";
        else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) $err["password"] = "le mot de passe doit contenir au moins une lettre majuscule et une lettre minuscule, un chiffre et doit être d'au moins 8 caractères";
        if (!isset($confirm_password) || $confirm_password == "") $err["confirm_password"] = "la confirmation du mot de passe est obligatoire";
        else if ($confirm_password != $password) $err["confirm_password"] = "la confirmation ne correspond pas au mot de passe";
        if (!isset($terms) || !$terms) $err["terms"] = "vous devez accepter les conditions d'utilisation";
        if (empty($err)) {
            $nom = htmlspecialchars(trim($nom));
            $prenom = htmlspecialchars(trim($prenom));
            $email = htmlspecialchars(trim($email));
            $password = htmlspecialchars(trim($password));
            $confirm_password = htmlspecialchars(trim($confirm_password));
            $filiere = htmlspecialchars(trim($filiere));
            $role = htmlspecialchars(trim($role));
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $date = date('Y-m-d H:i:s');
            $statut = "en_attente";
            include("../../database/config.php");
            try {
                $req = $db->prepare("INSERT INTO utilisateurs(nom, prenom, email, motdepasse,role, filiere,statut, date_inscription) VALUES(?,?,?,?,?,?,?,?)");
                $req->execute([$nom,$prenom,$email,$password_hash,$role,$filiere,$statut,$date]);
                if ($req==false){
                    header("Location: ../connexion/index.php?error=Erreur lors de l'inscription");
                    exit();
                }else{
                    header("Location: ../connexion/index.php?msg=Inscription réussie, en attente de validation par l'administrateur");
                    exit();
                }
            } catch (PDOException $e) {
                die("Erreur lors de l'inscription : " . $e->getMessage());
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISMO-SkillSwap – Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="inscription.css">

</head>

<body>
    <div class="left">
        <div class="brand-wrapper">
            <div class="jsp">
                <div class="brand">
                    <h1>ISMO-SkillSwap</h1>
                    <p>Plateforme d'échange de compétences ISMO Tétouan</p>
                </div>
            </div>
        </div>
        <div class="features">
            <h2>Rejoignez la communauté</h2>
            <div class="feature-item">
                <div class="feature-icon"><svg viewBox="0 0 24 24">
                        <path d="M20 6L9 17l-5-5" />
                    </svg></div>
                <span class="feature-text">Créez votre profil de compétences</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg viewBox="0 0 24 24">
                        <path d="M20 6L9 17l-5-5" />
                    </svg></div>
                <span class="feature-text">Partagez vos connaissances avec vos pairs</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg viewBox="0 0 24 24">
                        <path d="M20 6L9 17l-5-5" />
                    </svg></div>
                <span class="feature-text">Développez votre expertise reconnue</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg viewBox="0 0 24 24">
                        <path d="M20 6L9 17l-5-5" />
                    </svg></div>
                <span class="feature-text">Accédez à l'entraide collaborative</span>
            </div>
        </div>
        <p class="footer-text">© 2026 ISMO Tétouan – Institut Spécialisé dans les métiers de l'offshoring</p>
    </div>

    <main class="right">
        <div class="step <?= !empty($err) ? '' : 'active' ?>" id="step-role">
            <div class="role-step">
                <h2>Inscription</h2>
                <p class="subtitle">Choisissez votre rôle pour commencer</p>
                <div class="role-cards">
                    <div class="role-card" data-role="stagiaire" onclick="selectRole(this)">
                        <div class="role-icon stagiaire">🎓</div>
                        <div class="role-info">
                            <h3>Stagiaire</h3>
                            <p>Apprenez, échangez des compétences et progressez avec vos pairs</p>
                        </div>
                        <div class="role-check"></div>
                    </div>

                    <div class="role-card" data-role="formateur" onclick="selectRole(this)">
                        <div class="role-icon formateur">👨‍🏫</div>
                        <div class="role-info">
                            <h3>Formateur</h3>
                            <p>Encadrez les stagiaires et partagez votre expertise professionnelle</p>
                        </div>
                        <div class="role-check"></div>
                    </div>

                    <div class="role-card" data-role="admin" onclick="selectRole(this)">
                        <div class="role-icon admin">🛡️</div>
                        <div class="role-info">
                            <h3>Administrateur</h3>
                            <p>Gérez la plateforme, les filières et les utilisateurs ISMO</p>
                        </div>
                        <div class="role-check"></div>
                    </div>
                </div>

                <button class="btn-continue" id="btn-continue" onclick="goToForm()">Continuer →</button>
                <p class="login-link" style="margin-top:20px">Déjà un compte ? <a href="../connexion/index.html">Se connecter</a></p>
            </div>
        </div>

        <div class="step <?= !empty($err) ? 'active' : '' ?>" id="step-form">
            <div class="card">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="card-header">
                        <h2>Inscription</h2>
                        <p class="subtitle">Créez votre compte SkillSwap</p>
                    </div>

                    <div class="role-badge" onclick="goBack()" title="Changer de rôle">
                        <span id="badge-icon">🎓</span>
                        <span id="badge-label">Stagiaire</span>
                        <span style="margin-left:4px;opacity:0.6">✕</span>
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?php if(isset($_POST['nom'])) echo $_POST['nom']; ?>" placeholder="Nom de famille" />
                            <?php if (isset($err["nom"])) echo "<div style='color:red'>" . $err["nom"] . "</div>"; ?>
                        </div>
                        <div class="field">
                            <label>Prénom</label>
                            <input type="text" name="prenom" value="<?php if(isset($_POST['prenom'])) echo $_POST['prenom']; ?>" placeholder="Prénom" />
                            <?php if (isset($err["prenom"])) echo "<div style='color:red'>" . $err["prenom"] . "</div>"; ?>
                        </div>
                    </div>

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" placeholder="votre.email@ismo.ma" />
                        <?php if (isset($err["email"])) echo "<div style='color:red'>" . $err["email"] . "</div>"; ?>
                    </div>

                    <div class="field filiere-field" id="filiere-field">
                        <label>Filière</label>
                        <div class="select-wrap">

                            <select name="filiere">
                                <option value="">-- les choix --</option>
                                <option value="DEV 101">DEV 101 - Développement Digital</option>
                                <option value="INF 201">INF 201 - Infrastructure & Cloud</option>
                                <option value="AI 101">AI 101 - Intelligence Artificielle</option>
                                <option value="SEC 501">SEC 501 - Cybersécurité</option>
                            </select>
                            <?php if (isset($err["filiere"])) echo "<div style='color:red'>" . $err["filiere"] . "</div>"; ?>
                        </div>
                    </div>
                    <div class="field">
                        <label>Mot de passe</label>
                        <input type="password" name="password" value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>"placeholder="••••••••" />
                        <?php if (isset($err["password"])) echo "<div style='color:red'>" . $err["password"] . "</div>"; ?>
                    </div>

                    <div class="field">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" value="<?php if(isset($_POST['confirm_password'])) echo $_POST['confirm_password']; ?>" placeholder="••••••••" />
                        <?php if (isset($err["confirm_password"])) echo "<div style='color:red'>" . $err["confirm_password"] . "</div>"; ?>
                    </div>

                    <div class="terms">
                        <input type="checkbox" name="terms" id="terms-check" />
                        <span>J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique deconfidentialité</a></span>
                        <?php if (isset($err["terms"])) echo "<div style='color:red'>" . $err["terms"] . "</div>"; ?>
                    </div>

                    <input type="hidden" name="role" id="role-input" value="<?php if(isset($_POST['role'])) echo htmlspecialchars($_POST['role']); ?>"/>
                    <input type="submit" name="cre" class="btn-primary" id="btn-create-account" value="Créer mon compte"/>
                    <p class="login-link">Déjà un compte ? <a href="../connexion/index.html">Se connecter</a></p>
                    <?php if (isset($_GET['error'])) echo "<div style='color:red;text-align:center;margin-top:10px'>" . htmlspecialchars($_GET['error']) . "</div>"; ?>
                </form>
            </div>
        </div>

    </main>

    <div class="help-btn">?</div>

    <script>
        let selectedRole = null;

        const roleLabels = {
            stagiaire: {
                label: 'Stagiaire',
                icon: '🎓'
            },
            formateur: {
                label: 'Formateur',
                icon: '👨‍🏫'
            },
            admin: {
                label: 'Administrateur',
                icon: '🛡️'
            }
        };

        function selectRole(card) {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedRole = card.dataset.role;
            const btn = document.getElementById('btn-continue');
            btn.classList.add('enabled');
        }

        function goToForm() {
            if (!selectedRole) return;

            document.getElementById('badge-icon').textContent = roleLabels[selectedRole].icon;
            document.getElementById('badge-label').textContent = roleLabels[selectedRole].label;
            document.getElementById('role-input').value = selectedRole;

            const filiereField = document.getElementById('filiere-field');
            filiereField.classList.toggle('hidden', selectedRole !== 'stagiaire');


            document.getElementById('step-role').classList.remove('active');
            document.getElementById('step-form').classList.add('active');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function goBack() {
            document.getElementById('step-form').classList.remove('active');
            document.getElementById('step-role').classList.add('active');
        }

        window.addEventListener('DOMContentLoaded', function () {
            var stepForm = document.getElementById('step-form');
            if (stepForm.classList.contains('active')) {
                var roleInput = document.getElementById('role-input');
                if (roleInput.value) {
                    selectedRole = roleInput.value;
                    document.getElementById('badge-icon').textContent = roleLabels[selectedRole].icon;
                    document.getElementById('badge-label').textContent = roleLabels[selectedRole].label;
                    document.getElementById('filiere-field').classList.toggle('hidden', selectedRole !== 'stagiaire');
                }
            }
        });
    </script>
</body>

</html>
