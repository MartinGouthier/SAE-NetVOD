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
            //AuthnProvider::signout();
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
        <form method="POST" action="?action=sign-in">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <br>

            <button type="submit">Connexion</button>
        </form>
        
        HTML;
    
    }
} 