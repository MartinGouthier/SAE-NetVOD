<?php
namespace iutnc\netvod\render;

use iutnc\netvod\video\EpisodeSerie;

class EpisodeSerieRenderer implements Renderer {

    protected EpisodeSerie $episode;

    public function __construct(EpisodeSerie $episode) {
        $this->episode = $episode;
    }

    public function render(): string {
        $affichage  = "<div class='episode'>";
        $affichage .= "<h3>Épisode " . $this->episode->__get("numero") . " : " . htmlspecialchars($this->episode->__get("titre")) . "</h3>";
        $affichage .= "<p>Durée : " . $this->episode->__get("duree") . " min</p>";
        $affichage .= "<p>Résumé : " . htmlspecialchars($this->episode->__get("resume")) . "</p>";
        $affichage .= "<video controls width='400'>
                          <source src='" . htmlspecialchars($this->episode->__get("cheminFichier")) . "' type='video/mp4'>
                          Votre navigateur ne supporte pas la lecture vidéo.
                       </video>";
        $affichage .= "</div>";

        return $affichage;
    }
}
