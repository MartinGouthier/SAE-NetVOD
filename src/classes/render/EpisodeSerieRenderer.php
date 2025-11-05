<?php
namespace iutnc\netvod\render;

use iutnc\netvod\video\EpisodeSerie;

class EpisodeSerieRenderer implements Renderer {

    protected EpisodeSerie $episode;

    public function __construct(EpisodeSerie $episode) {
        $this->episode = $episode;
    }

    public function render(int $selecteur): string {
        if ($selecteur === self::COMPACT){
            $titre = "<a href='?action=display-episode&episode={$this->episode->__get("numero")}.'><h3>Épisode " . $this->episode->__get("numero") . " : " . htmlspecialchars($this->episode->__get("titre")) . "</h3></a>";
        } else
            $titre = "<h3>Épisode " . $this->episode->__get("numero") . " : " . htmlspecialchars($this->episode->__get("titre")) . "</h3>";

        $affichage = $titre;
        $affichage  .= "<div class='episode'>";
        $affichage .= "<p>Durée : " . $this->episode->__get("duree") . " min</p>";
        $affichage .= "<p>Résumé : " . htmlspecialchars($this->episode->__get("resume")) . "</p>";
        if ($selecteur === self::LONG) {
            $affichage .= "<video controls width='400'>
                              <source src='" . htmlspecialchars($this->episode->__get("cheminFichier")) . "' type='video/mp4'>
                              Votre navigateur ne supporte pas la lecture vidéo.
                           </video>";
        }
        $affichage .= "</div>";

        return $affichage;
    }
}
