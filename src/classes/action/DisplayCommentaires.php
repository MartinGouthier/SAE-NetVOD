<?php
namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayCommentaires extends ActionConnecte {
    public function GET(): string {
        $actionSecours = new CatalogueAction();
        if (!isset($_GET['id_serie']))
            return $actionSecours->GET();
        $idSerie = $_GET['id_serie'];
        if (!is_int($idSerie))
            return $actionSecours->GET();
        if (!filter_var($idSerie,FILTER_SANITIZE_NUMBER_INT))
            return $actionSecours->GET();
        $repo = NetvodRepository::getInstance();
        $serie = $repo->getSerieById($idSerie);
        if (!$serie){
            return $actionSecours->GET();
        }

        $renderer = new SerieRenderer($serie);
        return $renderer->render(Renderer::COMMENTAIRES);
    }
    public function POST(): string {
        return $this->GET();
    }
}
