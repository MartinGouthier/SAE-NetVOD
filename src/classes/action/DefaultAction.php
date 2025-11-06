<?php

namespace iutnc\netvod\action;

class DefaultAction extends Action {

    public function GET(): string {
        return <<<HTML
            <h1>Bienvenue sur NetVod</h1>
            <p>NetVod est une application qui vous permet de visionner vos séries préférées.</p>
            <p>Utilisez le menu pour accéder aux différentes fonctionnalités.</p>
        HTML;
    }

    public function POST(): string {
        return $this->GET();
    }
}
