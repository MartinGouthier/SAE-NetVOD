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

           // Si l'utilisateur demande la déconnexion via le bouton
        //TODO Faire le signout
        if (isset($_POST['signout'])) {
            AuthnProvider::signout();
            return "<p>Déconnexion réussie.</p>";
        }


        return $this->GET();
    }
}