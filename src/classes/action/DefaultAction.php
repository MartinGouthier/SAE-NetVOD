<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;
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
        try {
            $repo = NetvodRepository::getInstance();
            $id_user = (int) $repo->getUserInfo(AuthnProvider::getSignedInUser())[2];

            $html .= "<br><h2>Reprendre vos séries :</h2>";
            $listeSeriesRecentes = $repo->getSeriesEnCours($id_user);
            foreach ($listeSeriesRecentes as $serie){
                $renderer = new SerieRenderer($serie);
                $html .= $renderer->render(Renderer::SERIERECENTE);
            }
            $html .= "<br><h2>Mes séries préférées</h2>";
            $listeSeriesPref = $repo->getSeriesPref($id_user);
            foreach ($listeSeriesPref as $serie){
                $renderer = new SerieRenderer($serie);
                $html .= $renderer->render(Renderer::SERIEPREF);
            }
        } catch (AuthException){}
        return $html;
    }

    public function POST(): string {
        return $this->GET();
    }
}
