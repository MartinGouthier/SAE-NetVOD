<?php

namespace iutnc\netvod\auth;

use iutnc\netvod\exception\AuthException;
use iutnc\netvod\repository\NetvodRepository;

class AuthnProvider
{
    public static function signin(string $email, string $passwd2check): void {
        $bdd = NetvodRepository::getInstance();
        $tab = $bdd->getUserInfo($email);
        if (!$tab)
            throw new AuthException("<p>Identifiant ou mot de passe invalide</p>");
        $hash = $tab[0];
        if (!$hash ||!password_verify($passwd2check, $hash))
            throw new AuthException("<p>Identifiant ou mot de passe invalide</p>");
        if ($tab["role"] == 0)
            throw new AuthException("<p>Compte non activé, veuillez utiliser le lien fourni lors de l'activation</p>");
        $user = new User($email,$tab["role"],$tab["id"],$tab["first_name"],$tab["username"],$tab["last_name"],$tab["birthday"],$tab["favorite_genre"]);
        $_SESSION['user'] = serialize($user);

    }

    public static function signout(): void {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
    }

    public static function register( string $email, string $pass): String {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new AuthException("error : Email incorrect");
        if (strlen($pass) < 10)
            throw new AuthException("Erreur : Mot de passe trop court");
        $bdd = NetvodRepository::getInstance();
        if ($bdd->verifieEmailExiste($email)){
            throw new AuthException("Erreur : Email déja enregistré");
        }
        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost'=>12]);
        $token = $bdd->registerNewUser($email,$hash);
        return $token ;
    }       

    public static function getSignedInUser(): User {
        if (!isset($_SESSION['user']))
            throw new AuthException("Auth error : not signed in");
        return unserialize($_SESSION['user']);
    }
    

    //vérification avant de crée le token pour reset le password à un email donné
    public static function passWordToken($email) : String {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new AuthException("error : Email incorrect");
        $bdd = NetvodRepository::getInstance();
        if ($bdd->verifieEmailExiste($email)){
            $token = $bdd->registerpassWordToken($email);
            return $token ;
        }
        
         throw new AuthException("Erreur : Email non enregistré");
    }


    public static function AuthnResetPassWord($token, $password): void {
        if (strlen($password) < 10){
            throw new AuthException("Erreur : Mot de passe trop court");
        }
        $bdd = NetvodRepository::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost'=>12]);
        $bdd->registerNewpassWord($hash, $token);
    }


}