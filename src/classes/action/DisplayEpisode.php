<?php
namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
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
        $user = AuthnProvider::getSignedInUser();
        $id_user = (int) $repo->getUserInfo($user)['id'];
        $repo->updateEpisodeVisionne($id_user,$episodeId);
        $episodeDetails = $renderer->render(Renderer::LONG);


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
