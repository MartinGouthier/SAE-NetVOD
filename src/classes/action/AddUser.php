<?php
namespace iutnc\netvod\action;



use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;
use iutnc\netvod\repository\NetvodRepository;

/**
 * Classe représentant l'action d'ajout d'utilisateur
 */
class AddUser extends Action {

    // Si la requête HTTP est de type POST 
    public function POST(): string
    {

        $email = $_POST['email'];
        $password = $_POST['password'];
        try {
            AuthnProvider::register($email, $password);
            $html = <<<HTML
                <p> Inscription réussie ! Vous pouvez maintenant vous connecter</p>
                <a href="?action=sign-in">Se connecter</a>     
            HTML;
        } catch (AuthException $e) {

            $html = "<p>" . $e->getMessage() . "</p>";
            $html .= $this->GET();
        }
        return $html;
    }
        // Récupération et filtrage des valeurs


    // Si la requête est de type GET     
    public function GET(): string{
        return <<<HTML
        <h2>Inscription utilisateur</h2>
        <form method="post" action="?action=add-user">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="Password" name="Password" required><br><br>
            
            <label for="repassword">Confirmer votre Mot de passe :</label>
            <input type="password" id="RePassword" name="rePassword" required><br><br>

            <button type="submit">S'inscrire</button>
        </form>
        HTML;
    }
}