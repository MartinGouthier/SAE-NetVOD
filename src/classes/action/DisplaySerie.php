<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplaySerie extends ActionConnecte
{

    public function GET(): string
    {
        $action = new CatalogueAction();

        if (!(isset($_GET["id_serie"])&&filter_var($_GET["id_serie"],FILTER_SANITIZE_NUMBER_INT))){
            return $action->GET();
        } else {
            $id_serie = $_GET["id_serie"];
            $bdd = NetvodRepository::getInstance();
            $serie = $bdd->getSerieById($id_serie);
            if (!$serie){
                return $action->GET();
            }
            $renderer = new SerieRenderer($serie);
            $html = $renderer->render(Renderer::LONG);
        }
        return $html;
    }

    public function POST(): string
    {
        return $this->GET();
    }
}