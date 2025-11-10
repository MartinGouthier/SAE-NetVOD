<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;

class AddCommentaireEtNote extends ActionConnecte {

    public function GET(): string {
        $serieId = $_GET["id_serie"];
        $episodeId = $_GET["episode"];

        return <<<END
            <form method='post' action='?action=add-commentary-note'>
                <input type='hidden' name='serie' value='$serieId'>
                <input type='hidden' name='episode' value='$episodeId'>
        
                Note : <input type='number' name='note'><br><br>
                Commentaire : <br>
                <textarea name='commentaire' rows='4' cols='50'></textarea><br><br>
        
                <button type='submit'>Valider</button>
            </form>
        END;
    }

    public function POST(): string {
        $serieId = (int)$_POST['serie'];
        $episodeId = (int)$_POST['episode'];
        $note      = (int)filter_var($_POST['note'], FILTER_SANITIZE_NUMBER_INT);
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

        $_GET['episode'] = $episodeId;
        $display = new DisplayEpisode();
        $display->setMessage($message);
        return $display->GET();
    }
}
