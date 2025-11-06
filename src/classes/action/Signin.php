<?php
namespace iutnc\netvod\action;


use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;

/**
 * Classe représentant l'action de connexion utilisateur
 */
class Signin extends Action {

    public function POST(): string {

        // Si l'utilisateur demande la déconnexion via le bouton
        //TODO Faire le signout
        if (isset($_POST['signout'])) {
            AuthnProvider::signout();
            return "<p>Déconnexion réussie.</p>";
        }

        {
            $email = $_POST['email'];
            $password = $_POST['password'];
            try {
                AuthnProvider::signin($email, $password);
                $html = "<p>Connexion réussie</p>";
            } catch (AuthException $e){
                $html = "<b>" .$e->getMessage() . "</b>";
                $html .= $this->GET();
            }
            return $html;
        }

    }

        /**
         * Si le formulaire de connexion est soumis
         */
        
        public function GET() : string {
        
         // Si l'utilisateur est déjà connecté : afficher message + bouton déconnexion
        if (isset($_SESSION['user'])) {
            $email = $_SESSION['user'];
            return <<<HTML
            <div style="text-align:center; padding:20px;">
                <h2 id="sig">Vous êtes déjà connecté</h2>
               <form method="post" action="?action=sign-in">
                    <input type="hidden" name="signout" value="1">
                    <button type="submit">Se déconnecter</button>
                </form>
            </div>
            HTML;
        }

        // Formulaire de connexion
        return <<<HTML
        <h2>Connexion utilisateur</h2>
        <form method="POST" action="?action=sign-in">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
             <button type="button" id="togglePassword">Afficher</button>
            <br>


            <button type="submit">Connexion</button>
        </form>
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