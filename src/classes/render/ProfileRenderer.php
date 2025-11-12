<?php

namespace iutnc\netvod\render;

use iutnc\netvod\auth\User;
use iutnc\netvod\repository\NetvodRepository;

class ProfileRenderer implements Renderer {
    
    protected User $profile;

    public function __construct(User $profile) {
        $this->profile = $profile;
    }

    public function render(int $selecteur=0): string {

        $repo = NetvodRepository::getInstance();
        $nbEpVus = $repo->getNbEpisodesVus($this->profile->__GET("id"));
        $tauxCompletion = floor($nbEpVus / $repo->getTotalEpisodes() * 100);
        $nbComments = $repo->getNbCommentairesPostes($this->profile->__GET("id"));

        $affichage = "<div class='profile'>";
        $affichage .= "<h2>Profil de l'utilisateur</h2>";
        $affichage .= "<p><strong>Nom d'utilisateur :</strong> " . htmlspecialchars($this->profile->__GET("username")) . "</p>";
        $affichage .= "<p><strong>Email :</strong> " . htmlspecialchars($this->profile->__GET("email")) . "</p>";
        $affichage .= "<p><strong>Etat du compte :</strong> ";
        if ($this->profile->__GET("role") === 1) {
            $affichage .= "Actif";
        } else {
            $affichage .= "Temporaire";
        }
        $affichage .= "</p>";
        $affichage .= "<p><strong>Prenom :</strong> " . htmlspecialchars($this->profile->__GET("prenom")) . "</p>";
        $affichage .= "<p><strong>Nom :</strong> " . htmlspecialchars($this->profile->__GET("nom")) . "</p>";
        $affichage .= "<p><strong>Date de naissance :</strong> " . htmlspecialchars($this->profile->__GET("birthday")) . "</p>";
        $affichage .= "<p><strong>Genre préféré :</strong> " . htmlspecialchars($this->profile->__GET("genreFav")) . "</p>";
        $affichage .= "<p><strong>Nombre d'episodes vus :</strong> " . $nbEpVus . "</p>";
        $affichage .= "<p><strong>Taux de complétion du catalogue :</strong> " . $tauxCompletion . "%</p>";
        $affichage .= "<p><strong>Nombre de commentaires postés :</strong> " . $nbComments . "</p>";
        $affichage .= "<a href='?action=display-series-terminees'>Voir les séries terminées</a><br>";
        $affichage .= "<a href='?action=edit-profile'>Modifier le profil</a>";
        $affichage .= "</div>";
        return $affichage;
    }
}