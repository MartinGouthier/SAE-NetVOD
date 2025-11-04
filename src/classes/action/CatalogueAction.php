<?php

namespace iutnc\netvod\action;

class CatalogueAction extends Action {

    public function GET(): string {
        
        if (empty($_SESSION['Series'])) {
            return "<p>Aucune Series disponible. Créez-en une d'abord</p>";
        }
        
          foreach ($_SESSION['Series'] as $serie) {

            if ($serie instanceof Serie) {
                $renderer = new AudioListRender($serie);
                $res .= "<div class='serie-block'>";
                $res .= $renderer->render();
               
              
            } else {
                $res .= "<p>Erreur : élément non valide dans la session.</p>";
            }
        }

        return $res;

    }

    public function POST(): string {
        return $this->GET();

    }
}