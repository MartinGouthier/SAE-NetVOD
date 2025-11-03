<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\auth\AuthnProvider;

class Dispatcher {
    private string $action;

    public function __construct() {
        // "default" est une valeur arbitraire que l'on peut remplacer par ce que l'on veut
        // le but étant de ramener à la page d'accueil par la condition Default du switch
        $this->action = $_GET['action'] ?? "default";
    }

    public function run(): void {
        switch ($this->action) {
            default:
                $a = new DefaultAction();
                break;
        }
        $html = $a->execute();
        // Si un utilisateur est connecté, on l'affiche au dessus du contenu
        if (isset($_SESSION['user'])) {
            $html = "<p>Connecté en tant que " . AuthnProvider::getSignedInUser() . "</p>" . $html;
        }

        $this->renderPage($html);
    }

    //TODO A MODIFIER POUR ADAPTER LES ACTIONS
    private function renderPage(string $html): void {
        $pageComplete = <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>NetVod - Visionnage de séries en ligne</title>
                </head>
            
                <body>
                    <h1>NetVod</h1>
                    <p>Application de visionnage de séries</p>
                    <hr>
            
                    <p>
                        <a href="?action=sign-in">Connexion</a> |
                        <a href="?action=add-user">Inscription</a>
                    </p>
            
                    <hr>
            
                    <h2>Menu principal</h2>
                    <ul>
                        <li><a href="?action=default">Accueil</a></li>
                        <li><a href="?action=add-playlist">Créer une playlist vide</a></li>
                        <li><a href="?action=add-track">Ajouter une piste</a></li>
                        <li><a href="?action=playlists">Mes playlists</a></li>
                        <li><a href="?action=display-playlist">Afficher la playlist courante</a></li>
                    </ul>
            
                    <hr>
            
                    <div>
                        $html
                    </div>
                </body>
            </html>
        END;

        echo $pageComplete;
    }

}
