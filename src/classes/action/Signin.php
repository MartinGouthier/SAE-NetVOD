<?php
namespace iutnc\netvod\action;


/**
 * Classe représentant l'action de connexion utilisateur
 */
class Signin extends Action {

    public function POST(): string {
    
        // Si l'utilisateur demande la déconnexion via le bouton
        if (isset($_POST['signout'])) {
            // AuthnProvider::signout();
            return "<p>Déconnexion réussie.</p>";
        }


          $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Vérification des champs
            if (empty($email) || empty($password)) {
                return "<p>Veuillez remplir tous les champs.</p>";
            }

            // Vérification de la validité de l'email et du password
            if (AuthnProvider::signin($email, $password)) {
                return "<p>Connexion réussie</p>";
            }

                return "<p>Connexion refusé</p>";

        }

        /**
         * Si le formulaire de connexion est soumis
         */
        
        public function GET() : string {
        
         // Si l'utilisateur est déjà connecté : afficher message + bouton déconnexion
        if (isset($_SESSION['user'])) {
            $email = htmlspecialchars($_SESSION['user']['email']);
            return <<<HTML
            <div style="text-align:center; padding:20px;">
                <h2>Vous êtes déjà connecté</h2>
                <p>Connecté en tant que : <strong>$email</strong></p>
               <form method="post" action="?action=signin">
                    <input type="hidden" name="signout" value="1">
                    <button type="submit">Se déconnecter
                    </button>
            </div>
            HTML;
        }

        // Formulaire de connexion
        return <<<HTML
        <h2>Connexion utilisateur</h2>
        <form method="post" action="?action=signin">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="button" id="togglePassword">Afficher</button>
            <br><br>

            <button type="submit">Connexion</button>
        </form>
        <a href="?action=add-user">Créer un compte</a>
        <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
            
            // Change le texte du bouton selon l'état
            this.textContent = type === "password" ? "Afficher" : "Masquer";
        });
        </script>
        HTML;
    
    }
} 