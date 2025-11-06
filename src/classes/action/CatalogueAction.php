<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;


class CatalogueAction extends ActionConnecte {

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $tab = $pdo->getSeries();
        $res = "<h2>Catalogue des séries</h2>";
        $res .= $this->displayForm();
        foreach ($tab as $s) {
            $renderer = new SerieRenderer($s);
            $res .= $renderer->render(Renderer::COMPACT);
        }
        
        return $res;
    }

    public function POST(): string {
        $typeFiltre = $_POST['typeFiltre'];
        $filtre = $_POST['filtre'];
        $pdo = NetvodRepository::getInstance();
        if ($typeFiltre === "note"){
            $seriesTri = $pdo->getMoyennesSeries();
            $html = "<h2>Catalogue des séries</h2>";
            $html .= $this->displayForm();
            foreach($seriesTri as $serie){
                $renderer = new SerieRenderer($pdo->getSerieById($serie[1]));
                $html .= $renderer->render(Renderer::COMPACT);
            }
        } else {
            if (!filter_var($filtre, FILTER_SANITIZE_SPECIAL_CHARS))
                    return $this->GET();
            $html = "<h2>Catalogue des séries triés par $filtre</h2>";
            $html .= $this->displayForm();
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
            $series = $pdo->getSeriesFiltre($n, $filtre);
            foreach ($series as $s) {
                $renderer = new SerieRenderer($s);
                $html .= $renderer->render(Renderer::COMPACT);
            }
        }
        return $html;

    }

    public function displayForm() : string {
        return <<<HTML
        <form method="POST" action="?action=catalogue">
          <label for="typeFiltre">Filtrer les séries:</label>
          <select name="typeFiltre" id="typeFiltre">
            <option value="none">Aucun</option>
            <option value="motcle">Mot clé</option>
            <option value="genre">Genre</option>
            <option value="note">Note moyenne</option>
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