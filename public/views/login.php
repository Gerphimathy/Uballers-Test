<head>
    <meta charset="utf-8" />
    <link href="/css/styles.css" rel="stylesheet" />
</head>

<body>
    <header>
        <div id="login-form">
            <div class="navbar-field">
                <label>Adresse e-mail ou mobile</label>
                <input type="text" placeholder="Votre Login" id="login-login">
            </div>

            <div class="navbar-field">
                <label>Mot de passe</label>
                <input type="password" placeholder="Votre Mot De Passe" id="login-pass">
                <a href="#" class="navbar-link">Informations de compte oubliées ?</a>
            </div>
            <div class="navbar-button">
                <button id="login-confirm">Connexion</button>
            </div>
        </div>
    </header>
    <main id="signed-main">Signed</main>
    <main id="unsigned-main">
        <div id="signup-form">
            <h1>Inscription</h1>
            <h2>C'est gratuit (et ça le restera toujours)</h2>
            <div class="signup-form">
                <div>
                    <input type="text" placeholder="Prénom" id="signup-firstname">
                    <input type="text" placeholder="Nom de famille" id="signup-lastname">
                </div>
                <input type="text" placeholder="Numéro de mobile ou email" id="signup-login-1">
                <input type="text" placeholder="Confirmer numéro de mobile ou email" id="signup-login-2">
                <input type="password" placeholder="Nouveau mot de passe" id="signup-pass">
            </div>
            <div>
                <label>Date De Naissance</label>
                <div>
                    <input type="date" id="signup-date">
                    <a href="#">Pourquoi indiquer ma date de naissance ?</a>
                </div>

            </div>

            <div id="signup-gender" class="form-radio">
                <div class="form-radio">
                    <input type="radio" value="f" name="gender">
                    <label>Femme</label>
                </div>
                <div class="form-radio">
                    <input type="radio" value="m" name="gender">
                    <label>Homme</label>
                </div>
            </div>
            <div class="text-container">
                <p class="blurb">
                    En cliquant sur inscription, vous acceptez nos <a href="#">Conditions</a> et indiquez que vous avez lu notre
                    <a href="#">Politique d'utilisation des données</a>, y compris notre <a href="#">Utilisation des cookies.</a> Vous
                    pourrez recevoir des notification part exto de la part de Facebook et pouvez vous désabonner à tout moment.
                </p>
            </div>
            <p id="error-display">Placeholder</p>
            <button id="signup-confirm">Inscription</button>
        </div>
    </main>
    <footer></footer>

    <script type="module" src="/scripts/login.js"></script>
</body>
