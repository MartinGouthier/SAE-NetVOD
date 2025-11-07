<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplaySeriePref extends ActionConnecte {
    public function GET(): string
    {
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

    public function POST(): string
    {
        return $this->GET();
    }
}