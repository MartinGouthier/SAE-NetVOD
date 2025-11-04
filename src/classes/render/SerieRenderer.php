<?php
namespace iutnc\netvod\render;

use iutnc\netvod\video\Serie;

class SerieRenderer implements Renderer {

    protected Serie $serie;

    public function __construct(Serie $serie) {
        $this->serie = $serie;
    }

    public function render(): string {
        $affichage  = "<div class='serie'>";
        $affichage .= "<h2>" . htmlspecialchars($this->serie->__get("title")) . " (" . $this->serie->__get("annee") . ")</h2>";
        $affichage .= "<img src='" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'>";
        $affichage .= "<p><strong>Genre :</strong> " . htmlspecialchars($this->serie->__get("genre")) . "</p>";
        $affichage .= "<p><strong>Public :</strong> " . htmlspecialchars($this->serie->__get("typePublic")) . "</p>";
        $affichage .= "<p><strong>Description :</strong> " . htmlspecialchars($this->serie->__get("description")) . "</p>";

        // Rendu des épisodes
        $episodes = $this->serie->__get("episodes");
        if (!empty($episodes)) {
            $affichage .= "<h3>Épisodes :</h3>";
            foreach ($episodes as $episode) {
                $renderer = new EpisodeSerieRenderer($episode);
                $affichage .= $renderer->render();
            }
        }

        $affichage .= "</div>";

        return $affichage;
    }
}
