<?php
namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayCommentaires extends ActionConnecte {
    public function GET(): string {
        $idSerie = $_GET['id_serie'];

        $repo = NetvodRepository::getInstance();
        $serie = $repo->getSerieById($idSerie);

        $renderer = new SerieRenderer($serie);
        return $renderer->render(Renderer::COMMENTAIRES);
    }
    public function POST(): string {
        return $this->GET();
    }
}
