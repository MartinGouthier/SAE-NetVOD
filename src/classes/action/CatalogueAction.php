<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\render\CatalogueRenderer;

class CatalogueAction extends ActionConnecte {

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $tab = $pdo->getSeries();
        $res = "<h2>Catalogue des séries</h2>";
        $res .= $this->displayForm();
        foreach ($tab as $s) {
            $renderer = new CatalogueRenderer($s);
            $res .= $renderer->render();
        }
        
        return $res;
    }

    public function POST(): string {
        $typeFiltre = $_POST['typeFiltre'];
        $filtre = $_POST['filtre'];
        if (!filter_var($filtre,FILTER_SANITIZE_SPECIAL_CHARS))
            return $this->GET();
        else {
            $html = "<h2>Catalogue des séries triés par $filtre</h2>";
            $n = 0;
            switch ($typeFiltre) {
                case "motcle":
                    $n = 1;
                    break;
                case "genre":
                    $n = 2;
                    break;
                case "typePublic":
                    $n = 3;
                    break;
            }
            $pdo = NetvodRepository::getInstance();
            $series = $pdo->getSeriesFiltre($n,$filtre);

            foreach ($series as $s) {
                $renderer = new CatalogueRenderer($s);
                $html .= $renderer->render();
            }
            return $html;
        }

    }

    public function displayForm() : string {
        return <<<HTML
        <form method="POST" action="?action=catalogue">
          <label for="typeFiltre">Filtrer les séries:</label>
          <select name="typeFiltre" id="typeFiltre">
            <option value="none">Aucun</option>
            <option value="motcle">Mot clé</option>
            <option value="genre">Genre</option>
            <option value="typePublic">Type de publique</option>
          </select>
          <br>
          <label for="filtre">Filtre</label>
          <input type="text" name="filtre" id="filtre">
          <input type="submit" value="Filtrer">
        </form>
        HTML;
    }
}