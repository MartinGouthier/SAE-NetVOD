<?php
namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\render\EpisodeSerieRenderer;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\repository\NetvodRepository;

class DisplayEpisode extends ActionConnecte {


    public function GET(): string {
        $episodeId = $_GET['episode'];

        $repo = NetvodRepository::getInstance();
        $episode = $repo->getEpisodeById($episodeId);
        $_GET["id_serie"] = $episode->__get('id_serie');
        $_GET["episode"] = $episodeId;

        $renderer = new EpisodeSerieRenderer($episode);
        $id_user = $this->user->__GET("id");
        $repo->updateEpisodeVisionne($id_user,$episodeId);
        $episodeDetails = $renderer->render(Renderer::LONG);
        $avis = self::getHTMLformNote($episodeId);

        return $episodeDetails . $avis;
    }
    public function POST(): string {
        $serieId = intval($_POST['serie']);
        $episodeId = intval($_POST['episode']);
        $note = (int)filter_var($_POST['note'], FILTER_SANITIZE_NUMBER_INT);
        $commentaire = filter_var($_POST['commentaire'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $repo = NetvodRepository::getInstance();
        $userId = $this->user->__GET("id");

        if ($repo->notePresente($userId, $serieId)) {
            $message = "<b>Erreur :</b> vous avez déjà noté cette série.";
        }
        elseif ($note < 1 || $note > 5) {
            $message = "<b>Erreur :</b> la note doit être comprise entre 1 et 5.";
        }
        elseif ($commentaire === '') {
            $message = "<b>Erreur :</b> le commentaire ne peut pas être vide.";
        }
        else {
            $repo->ajouterNoteEtCommentaire($userId, $serieId, $note, $commentaire);
            $message = "<b>Commentaire ajouté avec succès.</b>";
        }
        $episode =$repo->getEpisodeById($episodeId);
        $renderer = new EpisodeSerieRenderer($episode);
        $id_user = $this->user->__GET("id");
        $repo->updateEpisodeVisionne($id_user,$episodeId);
        $episodeDetails = $renderer->render(Renderer::LONG);
        $avis = self::getHTMLformNote($episodeId);

        return $episodeDetails . $message . $avis;

    }

    public static function getHTMLformNote(int $episodeId) : string {
        $repo = NetvodRepository::getInstance();
        $episode = $repo->getEpisodeById($episodeId);
        $serieId = $episode->__GET("id_serie");

        return <<<END
            <form method='post' action='?action=display-episode'>
                <input type='hidden' name='serie' value='$serieId'> 
                <input type='hidden' name='episode' value ='$episodeId'>     
                Note : <input type='number' name='note'><br><br>
                Commentaire : <br>
                <textarea name='commentaire' rows='4' cols='50'></textarea><br><br>
        
                <button type='submit'>Valider</button>
            </form>
        END;
    }
}
