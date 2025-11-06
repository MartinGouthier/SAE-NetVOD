<?php

namespace iutnc\netvod\render;

class ProfileRenderer implements Renderer {
    
    protected array $profile;

    public function __construct(array $profile) {
        $this->profile = $profile;
    }

    public function render(int $selecteur=0): string {
        $affichage = "<div class='profile'>";
        $affichage .= "<h2>Profil de l'utilisateur</h2>";
        $affichage .= "<p><strong>Nom d'utilisateur :</strong> " . htmlspecialchars($this->profile['username']) . "</p>";
        $affichage .= "<p><strong>Email :</strong> " . htmlspecialchars($_SESSION['user']) . "</p>";
        $affichage .= "<p><strong>Etat du compte :</strong> ";
        if ($this->profile['role'] == 1) {
            $affichage .= "Actif";
        } else {
            $affichage .= "Temporaire";
        }
        $affichage .= "</p>";
        $affichage .= "<p><strong>Prenom :</strong> " . htmlspecialchars($this->profile['first_name']) . "</p>";
        $affichage .= "<p><strong>Nom :</strong> " . htmlspecialchars($this->profile['last_name']) . "</p>";
        $affichage .= "<p><strong>Date de naissance :</strong> " . htmlspecialchars($this->profile['date_of_birth']) . "</p>";
        $affichage .= "<p><strong>Genre préféré :</strong> " . htmlspecialchars($this->profile['favorite_genre']) . "</p>";
        $affichage .= "<p><strong>Nombre d'episodes vus :</strong> " . htmlspecialchars($this->profile['episodes_watched']) . "</p>";
        $affichage .= "<p><strong>Taux de complétion du catalogue :</strong> " . htmlspecialchars($this->profile['completion_rate']) . "%</p>";
        $affichage .= "<p><strong>Nombre de commentaires postés :</strong> " . htmlspecialchars($this->profile['comments_posted']) . "</p>";
        $affichage .= "<a href='?action=edit-profile'>Modifier le profil</a>";
        $affichage .= "</div>";
        return $affichage;
    }
}