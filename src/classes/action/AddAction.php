<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\auth\AuthnProvider;

class AddAction extends ActionConnecte {

    public function GET(): string {
        return <<<END
            <form method='post' action='?action=add'>
                ID de la série : <input type='number' name='id_serie'><br>
                <button type="submit">Ajouter à mes préférences</button>
            </form>
        END;
    }
    public function POST(): string {
        $idSerie = intval($_POST['id_serie']);
        $user = AuthnProvider::getSignedInUser();
        $idUser = $user->__get("id");
        $repo = NetvodRepository::getInstance();

        $prefs = $repo->getSeriesPref($idUser);
        foreach ($prefs as $serie) {
            if ($serie->__get("id") == $idSerie) {
                return "<p>Cette série est déjà dans vos préférences.</p>
                        <a href='?action=home'>Retour à l'accueil</a>";
            }
        }
        $repo->addSeriePref($idSerie, $idUser);
        return <<<END
            <p>Série ajoutée à vos préférences !</p>
            <a href='?action=home'>Retour à l'accueil</a>
        END;
    }
}

