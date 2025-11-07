<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DefaultAction extends Action {

    public function GET(): string {
        $html = <<<HTML
            <h2>Bienvenue sur NetVod</h2>
            <p>NetVod est une application qui vous permet de visionner vos séries préférées.</p>
            <p>Utilisez le menu pour accéder aux différentes fonctionnalités.</p>
        HTML;

        $html = "<h1>Mes séries préférées</h1>";
        $bdd = NetvodRepository::getInstance();
        $id_user = $bdd->getUserInfo(AuthnProvider::getSignedInUser())[2];
        $series = $bdd->getSeriesPref($id_user);
        foreach ($series as $serie){
            $renderer = new SerieRenderer($serie);
            $html .= $renderer->render(Renderer::SERIEPREF);
        }
        return $html;
    }

    public function POST(): string {
        return $this->GET();
    }
}
