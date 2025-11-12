<?php
namespace iutnc\netvod\render;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\video\EpisodeSerie;

class EpisodeSerieRenderer implements Renderer {

    protected EpisodeSerie $episode;

    public function __construct(EpisodeSerie $episode) {
        // On stocke l'épisode pour lequel on va générer le HTML
        $this->episode = $episode;
    }

    public function render(int $selecteur): string {
        $affichage = "<div class='episode'>";

        $repo = NetvodRepository::getInstance();
        $id_user = AuthnProvider::getSignedInUser()->__GET("id");

        // On ajoute le titre, éventuellement cliquable et avec indication "déjà vu"
        $affichage .= $this->renderTitre($selecteur, $id_user, $repo);

        // On affiche la durée et le résumé de l'épisode
        $affichage .= "<p>Durée : " . $this->episode->__get("duree") . " min</p>";
        $affichage .= "<p>Résumé : " . htmlspecialchars($this->episode->__get("resume")) . "</p>";

        // En mode LONG, on affiche le lecteur vidéo et un lien vers l'épisode suivant
        // Sinon, on affiche simplement l'image de la série
        if ($selecteur === Renderer::LONG) {
            $affichage .= $this->renderVideoEtSuivant($repo, $id_user);
        } else {
            $affichage .= $this->renderImageSerie($repo);
        }

        $affichage .= "</div>";
        return $affichage;
    }

    private function renderTitre(int $selecteur, int $id_user, NetvodRepository $repo): string {
        if ($selecteur === self::COMPACT) {
            // En mode COMPACT, le titre est cliquable et indique si l'épisode a déjà été vu
            $dejavu = $repo->estEpisodeVisionne($id_user, $this->episode->__get("id")) ? " (déjà vu)" : "";
            return "<a href='?action=display-episode&episode={$this->episode->__get("id")}.'>
                        <h2>Épisode " . $this->episode->__get("numero") . " : " .
                htmlspecialchars($this->episode->__get("titre")) . "$dejavu</h2>
                    </a>";
        } else {
            // Sinon, on affiche juste le titre sans lien
            return "<h2>Épisode " . $this->episode->__get("numero") . " : " .
                htmlspecialchars($this->episode->__get("titre")) . "</h2>";
        }
    }

    private function renderVideoEtSuivant(NetvodRepository $repo, int $id_user): string {
        // Affiche le lecteur vidéo pour l'épisode
        $affichage = "<video controls width='400'>
                          <source src='video\\" . htmlspecialchars($this->episode->__get("cheminFichier")) . "' type='video/mp4'>
                          Votre navigateur ne supporte pas la lecture vidéo.
                      </video>";

        // Vérifie s'il y a un épisode suivant en cours et ajoute un lien vers celui-ci
        $newEpisode = $repo->getProchainEpisodeEnCours($id_user, $this->episode->__get("id_serie"));
        if ($newEpisode) {
            $affichage .= "<a href='?action=display-episode&episode=" . $newEpisode->__get("id") . "'>
                               <p>Episode suivant</p>
                           </a>";
        }
        return $affichage;
    }

    private function renderImageSerie(NetvodRepository $repo): string {
        // Affiche l'image de la série cliquable vers l'épisode
        $serie = $repo->getSerieById($this->episode->__get("id_serie"));
        return "<a href='?action=display-episode&episode={$this->episode->__get("id")}' >
                    <img src='image\\" . htmlspecialchars($serie->__get("cheminImage")) . "' 
                         alt='Image de la série' width='200'>
                </a>";
    }
}
