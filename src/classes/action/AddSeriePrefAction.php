<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\auth\AuthnProvider;

class AddSeriePrefAction extends ActionConnecte {

    public function GET(): string {
        return "";
    }

    public function POST(): string {
        $idSerie = intval($_POST['id_serie']);
        $_GET["id_serie"] = $idSerie;
        $nouvAction = new DisplaySerie();
        $user = AuthnProvider::getSignedInUser();
        $repo = NetvodRepository::getInstance();
        $idUser = $repo -> getUserInfo($user)[0];

        $prefs = $repo->getSeriesPref($idUser);
        foreach ($prefs as $serie) {
            if ($serie->__get("id") === $idSerie) {
                return $nouvAction->GET();
            }
        }
        $repo->addSeriePref($idSerie, $idUser);
        return $nouvAction->GET();
    }
}
