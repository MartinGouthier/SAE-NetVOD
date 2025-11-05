<?php
namespace iutnc\netvod\action;

use iutnc\netvod\render\EpisodeSerieRenderer;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayEpisode extends ActionConnecte {

    public function GET(): string {
        $episodeId = $_GET['episode'];

        $repo = NetvodRepository::getInstance();

        $episode = $repo->getEpisodeById($episodeId);
        $_GET["id_serie"] = $episode->__get('id_serie');
        $renderer = new EpisodeSerieRenderer($episode);
        $episodeDetails = $renderer->render(Renderer::LONG);

        $avis = new AddCommentaireEtNote();
        $commentForm = $avis->GET();
        return $episodeDetails . $commentForm;
    }
    public function POST(): string {
        return $this->GET();
    }
}
