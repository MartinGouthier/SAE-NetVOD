<?php
namespace iutnc\netvod\render;

use iutnc\netvod\video\Serie;

class CatalogueRenderer implements Renderer {

    protected Serie $serie;

    public function __construct(Serie $serie) {
        $this->serie = $serie;
    }

    public function render(): string {
        $affichage  = "<div class='serie'>";
        $affichage .= "<h2>" . htmlspecialchars($this->serie->__get("title")) . " (" . $this->serie->__get("annee") . ")</h2>";
        $affichage .= "<a href=?action=ActionAfficheSerie?id='". $this->serie->__get("id") ."'><img src='". "image\\" . htmlspecialchars($this->serie->__get("cheminImage")) . "' alt='Image de la série' width='200'></a>";
        $affichage .= "</div>";

        return $affichage;
    }
}
