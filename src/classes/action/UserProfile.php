<?php
namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\render\ProfileRenderer;

class UserProfile extends ActionConnecte {

    public function GET(): string {
        $renderer = new ProfileRenderer($this->user);
        return $renderer->render();
    }

    public function POST(): string {
        return $this->GET();
    }
}