<?php
namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;

class DisplayEpisode extends Action {

    public function GET(): string {
        $serieId = $_GET['serie'];
        $episodeId = $_GET['episode'];

        $repo = NetvodRepository::getInstance();
        $episode = $repo->getEpisodeById($episodeId);

        $episodeDetails = <<<END
            <h3>Épisode: {$episode['titre']}</h3>
            <p>{$episode['description']}</p>
            <video controls>
                <source src="{$episode['video_url']}" type="video/mp4">
                Votre navigateur ne supporte pas la vidéo.
            </video><br><br>
        END;

        $commentForm = <<<END
            <form method='post' action='?action=add-commentary-note'>
                <input type='hidden' name='serie' value='$serieId'>
                <input type='hidden' name='episode' value='$episodeId'>
            
                Note : <input type='number' name='note' min="1" max="5"><br><br>
                Commentaire : <br>
                <textarea name='commentaire' rows='4' cols='50'></textarea><br><br>
            
                <button type='submit'>Valider</button>
            </form>
        END;
        return $episodeDetails . $commentForm;
    }
    public function POST(): string {
        return $this->GET();
    }
}
