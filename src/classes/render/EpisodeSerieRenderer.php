<?php
namespace iutnc\netvod\render;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\video\EpisodeSerie;


class EpisodeSerieRenderer implements Renderer {

    protected EpisodeSerie $episode;

    public function __construct(EpisodeSerie $episode) {
        $this->episode = $episode;
    }

    public function render(int $selecteur): string {
        $affichage  = "<div class='episode'>";
        $repo = NetvodRepository::getInstance();
        $id_user = intval($repo->getUserInfo(AuthnProvider::getSignedInUser())[2]);
        if ($selecteur === self::COMPACT){
            $dejavu = $repo->estEpisodeVisionne($id_user,$this->episode->__get("id")) ? " (déja vu)" : "";
            $titre = "<a href='?action=display-episode&episode={$this->episode->__get("id")}.'><h3>Épisode " . $this->episode->__get("numero") . " : " . htmlspecialchars($this->episode->__get("titre")) . "$dejavu</h3></a>";
        }
        else {
            $titre = "<h3>Épisode " . $this->episode->__get("numero") . " : " . htmlspecialchars($this->episode->__get("titre")) . "</h3>";
        }
        $affichage .= $titre;
        $affichage .= "<p>Durée : " . $this->episode->__get("duree") . " min</p>";
        $affichage .= "<p>Résumé : " . htmlspecialchars($this->episode->__get("resume")) . "</p>";
        if ($selecteur === Renderer::LONG) {
            $affichage .= "<video controls width='400'>
                          <source src='" ."video\\" . htmlspecialchars($this->episode->__get("cheminFichier")) . "' type='video/mp4'>
                          Votre navigateur ne supporte pas la lecture vidéo.
                       </video>";



            $newEpisode = $repo->getProchainEpisodeEnCours($id_user,$this->episode->__get("id_serie"));

            if ($newEpisode) {
                $affichage .= "<a href=?action=display-episode&episode=" . $newEpisode->__get("id") . "><p>Episode suivant</p></a>";
            }
        } else {
            $serie = $repo->getSerieById($this->episode->__get("id_serie"));
            $affichage .= "<a href=?action=display-episode&episode={$this->episode->__get("id")} ><img src='". "image\\" . htmlspecialchars($serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
        }
        $affichage .= "</div>";

        return $affichage;
    }
}
