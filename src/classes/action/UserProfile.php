<?php
namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\render\ProfileRenderer;

class UserProfile extends ActionConnecte {

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $profile = $pdo->getUserInfo($_SESSION['user']);
        var_dump($profile);
        $renderer = new ProfileRenderer($profile);
        return $renderer->render();
    }

    public function POST(): string {
        // Implementation of POST method for user profile
    }
}