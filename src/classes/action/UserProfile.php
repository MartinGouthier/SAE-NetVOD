<?php
namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;

class UserProfile extends ActionConnecte {

    public function GET(): string {
        $repo = NetvodRepository::getInstance();
        $nbEpVus = $repo->getNbEpisodesVus($this->user->__GET("id"));
        $tauxCompletion = floor($nbEpVus / $repo->getTotalEpisodes() * 100);
        $nbComments = $repo->getNbCommentairesPostes($this->user->__GET("id"));
        $html =  <<<HTML
                    <div class='userprofile'>
                    <h2>Profil de l'utilisateur</h2>
                    <p><strong>Nom d'utilisateur : </strong>{$this->user->__GET("username")} </p>
                    <p><strong>Email : </strong>{$this->user->__GET("email")}</p>
                    <p><strong>Etat du compte : </strong>
                    HTML;
        if ($this->user->__GET("role") === 1) {
            $html .= "Actif";
        } else {
            $html .= "Temporaire";
        }
        $html .= <<<HTML
                    </p>
                    <p><strong>Prenom :</strong>  {$this->user->__GET("prenom")} </p>
                    <p><strong>Nom :</strong> {$this->user->__GET("nom")} </p>
                    <p><strong>Date de naissance :</strong> {$this->user->__GET("birthday")}</p>
                    <p><strong>Genre préféré : </strong>{$this->user->__GET("genreFav")}</p>
                    <p><strong>Nombre d'episodes vus : </strong>$nbEpVus </p>
                    <p><strong>Taux de complétion du catalogue : </strong>$tauxCompletion%</p>
                    <p><strong>Nombre de commentaires postés : </strong>$nbComments</p>
                    <a href='?action=display-series-terminees'>Voir les séries terminées</a><br>
                    <a href='?action=edit-profile'>Modifier le profil</a>
                    </div>
                    HTML;
        return $html;
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