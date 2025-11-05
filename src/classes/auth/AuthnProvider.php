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
            throw new AuthException("Auth error : invalid credentials");
        $hash = $tab[0];
        if (!$hash ||!password_verify($passwd2check, $hash))
            throw new AuthException("Auth error : invalid credentials");
        $_SESSION['user'] = $email;

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

    public static function getSignedInUser( ): string {
        if (!isset($_SESSION['user']))
            throw new AuthException("Auth error : not signed in");
        return $_SESSION['user'];
    }

}