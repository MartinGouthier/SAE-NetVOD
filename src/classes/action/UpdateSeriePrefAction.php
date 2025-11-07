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
        $nouvAction = new DefaultAction();
        $idSerie = ($_POST['id_serie']);
        $typeModif = $_POST['typeModif'];
        $repo = NetvodRepository::getInstance();
        $idUser = $repo->getUserInfo(AuthnProvider::getSignedInUser())[2];

        if ($typeModif === "ajout") {
            $repo->addSeriePref($idSerie,$idUser);
        } else {
            $repo->retirerSeriePref($idSerie, $idUser);
        }
        return $nouvAction->GET();
    }
}
