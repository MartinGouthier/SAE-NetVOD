<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\render\CatalogueRenderer;

class CatalogueAction extends Action {

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $tab = $pdo->getSeries();
        $res = "<h2>Catalogue des séries</h2>";
        foreach ($tab as $s) {
            $renderer = new CatalogueRenderer($s);
            $res .= $renderer->render();
        }
        
        return $res;
    }

    public function POST(): string {
        return $this->GET();

    }
}