<?php

namespace iutnc\netvod\action;

use iutnc\netvod\dispatch\Dispatcher;
use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\auth\AuthnProvider;

class UpdateSeriePrefAction extends ActionConnecte {

    public function GET(): string {
        $action = new CatalogueAction();
        return $action->execute();
    }

    public function POST(): string {

        $idSerie = ($_POST['id_serie']);
        echo($idSerie);
        $typeModif = $_POST['typeModif'];
        $repo = NetvodRepository::getInstance();
        $idUser = $repo->getUserInfo(AuthnProvider::getSignedInUser())[2];

        if ($typeModif === "ajout") {
            $_GET["id_serie"] = $idSerie;
            $repo->addSeriePref($idSerie,$idUser);
            $nouvAction = new DisplaySerie();

        } else {
            $repo->retirerSeriePref($idSerie, $idUser);
            $nouvAction = new DisplaySeriePref();
        }
        return $nouvAction->GET();
    }
}
