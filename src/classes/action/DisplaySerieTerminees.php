<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplaySerieTerminees extends ActionConnecte {

    public function GET(): string {
        
        $repo = NetvodRepository::getInstance();
        $listeSeriesTerminees = $repo->getSeriesTerminees($this->user->__GET("id"));
        $html = "<h2>Mes séries terminées :</h2>";
        foreach ($listeSeriesTerminees as $s) {
            $render = new SerieRenderer($s);
            $html .= $render->render(Renderer::COMPACT);
        }
        return $html;
    }

    public function POST(): string {
        return $this->GET();
    }
}