<?php
namespace iutnc\netvod\action;


/**
 * Classe représentant l'action d'ajout d'utilisateur
 */
class AddUser extends Action {

    // Si la requête HTTP est de type POST 
    public function POST(): string {

        // Récupération et filtrage des valeurs
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['Password'] ?? '';
        $repassword = $_POST['rePassword'] ?? '';

        // Vérification minimale
        if (empty($email) || empty($password)) {
            return "<p>Veuillez remplir tous les champs</p>";
        }

        // Vérification mot de passe
        if (strcmp($password, $repassword) !== 0) {
             return "<p>Veuillez saisir le même mot de passe</p>";
        }

        return "<p> Inscription réussie ! Vous pouvez maintenant vous connecter</p>";
    }

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