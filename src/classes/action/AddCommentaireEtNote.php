<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;

class AddCommentaireEtNote extends ActionConnecte {

    public function GET(): string {
        $serieId = $_GET['serie'];

        return <<<END
            <form method='post' action='?action=add-commentary-note'>
                <input type='hidden' name='serie' value='$serieId'>
        
                Note : <input type='number' name='note'><br><br>
                Commentaire : <br>
                <textarea name='commentaire' rows='4' cols='50'></textarea><br><br>
        
                <button type='submit'>Valider</button>
            </form>
        END;
    }

    public function POST(): string {
        $serieId = $_POST['serie'];
        $note = $_POST['note'];
        $commentaire = $_POST['commentaire'];

        $userId = AuthnProvider::getSignedInUser();

        $repo = NetvodRepository::getInstance();

        if ($repo->notePresente($userId, $serieId)) {
            return <<<END
                <b>Erreur :</b> vous avez déjà noté cette série.<br>
                <a href='?action=displayEpisode&serie=$serieId'>Retour</a>
            END;
        }

        if ($note === '' || $note < 1 || $note > 5) {
            return <<<END
                <b>Erreur :</b> la note doit être un nombre entre 1 et 5.<br>
                <a href='?action=add-commentary-note&serie=$serieId'>Réessayer</a>
            END;
        }

        if (trim($commentaire) === '') {
            return <<<END
                <b>Erreur :</b> le commentaire ne peut pas être vide.<br>
                <a href='?action=add-commentary-note&serie=$serieId'>Réessayer</a>
            END;
        }

        $repo->ajouterNoteEtCommentaire($userId, $serieId, $note, $commentaire);

        return <<<END
            <b>Commentaire ajouté avec succès.</b><br>
            <a href='?action=displayEpisode&serie=$serieId'>Retour à la série</a>
        END;
    }
}
