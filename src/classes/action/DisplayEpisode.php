<?php
namespace iutnc\netvod\action;

use iutnc\netvod\render\EpisodeSerieRenderer;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayEpisode extends ActionConnecte {

    private string $message = '';

    public function setMessage(string $msg): void {
        $this->message = $msg;
    }
    public function GET(): string {
        $episodeId = $_GET['episode'];

        $repo = NetvodRepository::getInstance();
        $episode = $repo->getEpisodeById($episodeId);
        $_GET["id_serie"] = $episode->__get('id_serie');
        $_GET["episode"] = $episodeId;

        $renderer = new EpisodeSerieRenderer($episode);
        $episodeDetails = $renderer->render(Renderer::COMPACT);

        $avis = new AddCommentaireEtNote();
        $commentForm = $avis->GET();

        return $episodeDetails
            . ($this->message !== '' ? "<div class='erreur'>$this->message</div>" : '')
            . $commentForm;
    }
    public function POST(): string {
        return $this->GET();
    }
}
