<?php
namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;
use iutnc\netvod\exception\TokenException;

/**
 * Classe représentant l'action de changement de mot de passe
 */
class ResetPassWord extends Action {

    /**
     * Méthode POST — traitement du formulaire de réinitialisation
     */
    public function POST(): string {
        try {
            // Récupération des champs du formulaire
            $password   = $_POST['password']   ?? null;
            $repassword = $_POST['rePassword'] ?? null;
            $token      = $_POST['token']      ?? ($_GET['token'] ?? null);

            // Vérifications de base
            if (!$token) {
                throw new TokenException("Token manquant ou invalide.");
            }

            if (!$password || !$repassword) {
                return "<p>Veuillez remplir tous les champs.</p>";
            }

            if ($password !== $repassword) {
                return "<p>Les mots de passe ne correspondent pas.</p>";
            }

            // Appel au fournisseur d'authentification pour réinitialiser
            AuthnProvider::AuthnResetPassWord($token, $password);

        } catch (AuthException $e) {
            return "<p>" . $e->getMessage() . "</p>";
        } catch (TokenException $e) {
            return "<p>" . $e->getMessage() . "</p>";
        }

        return "<p>Changement de mot de passe réussi, vous pouvez maintenant vous connecter.</p>";
    }

    /**
     * Méthode GET — affichage du formulaire de réinitialisation
     */
    public function GET(): string {
        // Si l'utilisateur est déjà connecté : on empêche le reset
        if (isset($_SESSION['user'])) {
            return <<<HTML
            <div style="text-align:center; padding:20px;">
                <h2 id="sig">Vous êtes déjà connecté, vous ne pouvez pas changer de mot de passe.</h2>
            </div>
            HTML;
        }

        // Récupération du token depuis l’URL
        $token = htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES);

        // Si aucun token, on affiche une erreur
        if (empty($token)) {
            return "<p>Token manquant ou invalide.</p>";
        }

        // Formulaire de réinitialisation
        return <<<HTML
        <h2>Changement de mot de passe</h2>
        <form method="POST" action="?action=resetpassword">
            <!-- renvoie le token dans un champ caché -->
            <input type="hidden" name="token" value="$token">

            <label for="password">Nouveau mot de passe :</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <label for="rePassword">Confirmer le nouveau mot de passe :</label><br>
            <input type="password" id="rePassword" name="rePassword" required><br><br>

            <button type="submit">Changer le mot de passe</button>
        </form>
        HTML;
    }
}
