<?php

namespace iutnc\netvod\auth;

use iutnc\netvod\exception\AuthException;
use iutnc\netvod\repository\NetvodRepository;

class Authz{

    public static function checkRole(int $n) : bool {

        $pdo = NetvodRepository::getInstance();
        $user = unserialize($_SESSION['user']);
        if (!$user){
            throw new AuthException("Erreur : Aucun user connecté");
        }
        $role = $pdo->getUserInfo($user)[1];
        return ($n === (int)$role);
    }

    public static function checkPlaylistCurrOwner(int $idPlaylist) : bool{
        if (Authz::checkRole(100))
            return true;
        $pdo = NetvodRepository::getInstance();
        return $pdo->checkPlaylistOwner(unserialize($_SESSION['user']), $idPlaylist);
    }
}