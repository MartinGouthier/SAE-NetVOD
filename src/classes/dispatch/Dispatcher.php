<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\AddCommentaireEtNote;
use iutnc\netvod\action\DisplayCommentaires;
use iutnc\netvod\action\UpdateSeriePrefAction;
use iutnc\netvod\action\DisplayEpisode;
use iutnc\netvod\action\DisplaySerie;
use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\action\DefaultAction;
use iutnc\netvod\action\CatalogueAction;
use iutnc\netvod\action\AddUser;
use iutnc\netvod\action\Signin;


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
            case 'add-user':
                $a = new AddUser();
                break;
            case 'add-commentary-note':
                $a = new AddCommentaireEtNote();
                break;
            case 'display-episode':
                $a = new DisplayEpisode();
                break;
             case 'sign-in':
                $a = new Signin();
                break;
            case 'catalogue':
                $a = new CatalogueAction();
                break;
            case 'display-serie':
                $a = new DisplaySerie();
                break;
            case 'update-series-pref':
                $a =new UpdateSeriePrefAction();
                break;
            case 'display-commentaires':
                $a =new DisplayCommentaires();
                break;
        }
        $html = $a->execute();
        // Si un utilisateur est connecté, on l'affiche au dessus du contenu
        if (isset($_SESSION['user'])) {
            $html = "<p>Connecté en tant que " . AuthnProvider::getSignedInUser() . "</p>" . $html;
        }

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $pageComplete = <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>NetVod - Visionnage de séries en ligne</title>
                    <link rel="stylesheet" href="style.css">
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
                        <li><a href="?action=catalogue">Catalogue</a></li>
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
