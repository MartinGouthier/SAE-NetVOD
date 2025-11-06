<?php
namespace iutnc\netvod\render;

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
        else
            $affichage .= "<a href=?action=display-serie&id_serie=". $this->serie->__get("id") ."><img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
        $affichage .= "<p><strong>Genre :</strong> " . htmlspecialchars($this->serie->__get("genre")) . "</p>";
        $affichage .= "<p><strong>Public :</strong> " . htmlspecialchars($this->serie->__get("typePublic")) . "</p>";
        $affichage .= "<p><strong>Description :</strong> " . htmlspecialchars($this->serie->__get("description")) . "</p>";

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
