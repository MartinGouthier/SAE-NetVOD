<?php

namespace iutnc\netvod\action;

class CatalogueAction extends Action {

    public function GET(): string {
        return <<<HTML
            <h2>Catalogue des séries</h2>
            <!-- TODO catalogue -->
        HTML;
    }

    public function POST(): string {
        return $this->GET();
    }
}