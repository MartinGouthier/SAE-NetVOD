<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\render\SerieRenderer;

class CatalogueAction extends Action {

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $renderer = new SerieRenderer($pdo->getSeries());
        $res = $renderer->render();
        return $res;
    }

    public function POST(): string {
        return $this->GET();

    }
}