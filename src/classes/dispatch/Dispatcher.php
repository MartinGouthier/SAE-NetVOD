<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\AddCommentaireEtNote;
use iutnc\netvod\action\DisplayEpisode;
use iutnc\netvod\action\DisplaySerie;
use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\action\DefaultAction;
use iutnc\netvod\action\CatalogueAction;
use iutnc\netvod\action\AddUser;
use iutnc\netvod\action\Signin;
use iutnc\netvod\action\UserProfile;
use iutnc\netvod\action\ResetPassWord;
use iutnc\netvod\action\DemandeResetPassWord;

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
            case 'user-act':
                $a = new UserActivation();
                break;
            case 'display-serie':
                $a = new DisplaySerie();
                break;
            case 'password-perdu':
                $a = new DemandeResetPassWord();
                break;
            case 'resetPassWord':
                $a = new ResetPassWord();
                break;
            case 'user-profile':
                $a = new UserProfile();
                break;
        }
        $html = $a->execute();
        // Si un utilisateur est connecté, on l'affiche au dessus du contenu
        if (isset($_SESSION['user'])) {
            $html = "<p>Connecté en tant que <strong>" . AuthnProvider::getSignedInUser() . "</strong></p>" . $html;
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
                    <link rel="stylesheet" href="style.css">
                </head>
            
                <body>
                    <h1 id="logo"><img src="image\\fishing-net.png" alt="Logo NetVod">VOD</h1>
                    <p>Application de visionnage de séries</p>
                    <hr>
            
                    <p>
                        <a href="?action=sign-in">Connexion</a> |
                        <a href="?action=add-user">Inscription</a> |
                        <a href="?action=user-profile">Profil utilisateur</a>
                    </p>
            
                    <hr>
            
                    <h2>Menu principal</h2>
                    <ul>
                        <li><a href="?action=default">Accueil</a></li>
                        <li><a href="?action=catalogue">Catalogue</a></li>
                        <li><a href="?action=preference">Mes préférences</a></li>
                        <li><a href="?action=viewed">Mes visionages</a></li>
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
