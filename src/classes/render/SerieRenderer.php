<?php
namespace iutnc\netvod\render;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\video\Serie;

class SerieRenderer implements Renderer {

    protected Serie $serie;

    public function __construct(Serie $serie) {
        $this->serie = $serie;
    }

    public function render(int $selecteur): string {
        $affichage  = "<div class='serie'>";
        $affichage .= "<h2>" . htmlspecialchars($this->serie->__get("title")) . " (" . $this->serie->__get("annee") . ")</h2>";
        if ($selecteur === Renderer::LONG)
            $affichage .= "<img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'>";
        elseif ($selecteur === Renderer::SERIERECENTE){
            $repo = NetvodRepository::getInstance();
            $id_user = intval($repo->getUserInfo(AuthnProvider::getSignedInUser())[2]);
            $episode = $repo->getProchainEpisodeEnCours($id_user,$this->serie->__get("id"));
            if (!$episode)
                $affichage .= "<a href=?action=display-serie&id_serie=". $this->serie->__get("id") ."><img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
            else
                $affichage .= "<a href=?action=display-episode&episode=". $episode->__get("id") ."><img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
        }
        else
            $affichage .= "<a href=?action=display-serie&id_serie=". $this->serie->__get("id") ."><img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
        $affichage .= "<p><strong>Genre :</strong> " . htmlspecialchars($this->serie->__get("genre")) . "</p>";
        $affichage .= "<p><strong>Public :</strong> " . htmlspecialchars($this->serie->__get("typePublic")) . "</p>";
        $affichage .= "<p><strong>Description :</strong> " . htmlspecialchars($this->serie->__get("description")) . "</p>";

        if ($selecteur === Renderer::LONG || $selecteur === Renderer::COMPACT || $selecteur === Renderer::SERIERECENTE) {
            $affichage .= <<<HTML
                         <form action=?action=update-series-pref method=POST>
                         <input type = "hidden" name="id_serie" value={$this->serie->__GET("id")}>
                         <input type = "hidden" name="typeModif" value = "ajout">
                         <input type="submit" value = "Ajouter aux favoris">
                        </form>
            HTML;
        }
        elseif ($selecteur === Renderer::SERIEPREF){
            $affichage .= <<<HTML
                         <form action=?action=update-series-pref method=POST>
                         <input type = "hidden" name="id_serie" value={$this->serie->__GET("id")}>
                         <input type = "hidden" name="typeModif" value = "retrait">
                         <input type="submit" value = "Retirer des favoris">
                        </form>
            HTML;
        }
        // Rendu des épisodes
        if ($selecteur === Renderer::LONG) {
            $episodes = $this->serie->__get("episodes");
            if (!empty($episodes)) {
                $affichage .= "<h3>Épisodes :</h3>";
                foreach ($episodes as $episode) {
                    $renderer = new EpisodeSerieRenderer($episode);
                    $affichage .= $renderer->render(Renderer::COMPACT);
                }
            }
        }

        $affichage .= "</div>";

        return $affichage;
    }
}
