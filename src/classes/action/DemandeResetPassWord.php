<?php
namespace iutnc\netvod\action;


use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;
use iutnc\netvod\exception\TokenException;
/**
 * Classe représentant l'action de demandé le changement de password, gère la création de l'url
 */
class DemandeResetPassWord extends Action {

    // Si l'utilisateur demande le reset via le bouton
    public function POST(): string {

        
        // Si le reset est demandé, affiche l'url 
        try {
      
        $email = $_POST['email'];
        
        $token = AuthnProvider::passWordToken($email);
        
        $resetUrl = "http://localhost/td/SAE-NetVOD/?action=resetpassword&token=" . urlencode($token);
        
        $html = "<p>Voici votre lien pour changer votre mot de passe, valable pendant 5 minutes :</p><p><a href='$resetUrl'>$resetUrl</a></p>";
    

        } catch (AuthException $e) {

            return "<p>" . $e->getMessage() . "</p>";
      
        } catch (TokenException $e) {

            return "<p>" . $e->getMessage() . "</p";
        }
        
        return $html ;
      
    }
    

        /**
         * Le formulaire de demande de reset est soumis
         */
        public function GET() : string {
        
         // Si l'utilisateur est déjà connecté : afficher message
        if (isset($_SESSION['user'])) {
            $email = $_SESSION['user'];
            return <<<HTML
            <div style="text-align:center; padding:20px;">
                <h2 id="sig">Vous êtes déjà connecté, vous ne pouvez pas changé de mot de passe</h2>
            </div>
            HTML;
        }

        // Formulaire
        return <<<HTML
        <h2>Mot de Passe Oublié</h2>
        <form method="POST" action="?action=password-perdu">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>

            <button type="submit">Changé le mot de passe</button>
        </form>
        
        HTML;
    
    }
} 