<?php
namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\render\EpisodeSerieRenderer;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayCommentaires extends ActionConnecte {
    public function GET(): string {
        $episodeId = $_GET['episode'];

        $repo = NetvodRepository::getInstance();
        $episode = $repo->getEpisodeById($episodeId);
        $serie = $repo->getSerieById($episode->__get('id_serie'));

        $renderer = new SerieRenderer($serie);
        return $renderer->render(SerieRenderer::COMMENTAIRES);
    }
    public function POST(): string {
        return $this->GET();
    }
}
