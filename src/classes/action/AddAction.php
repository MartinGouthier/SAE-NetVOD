    <?php

namespace iutnc\netvod\action;



use iutnc\netvod\repository\NetvodRepository;

class AddAction extends Action{
    public function GET(): string
    {
        return <<<END
                <form method='post' action=?action=add><br>
                Série : <input type='text' name='nom'><br>
                <button type="submit">Ajouter à mes préférences</button> </form>
        END;

    }

    public function POST(): string
    {
        $nom = $_POST['nom'];
        $pl = new Liste($nom);

        $pdo = NetvodRepository::getInstance();
        $pdo->sauvegarderNouvelleListe($pl);
        $pdo->saveUserListe(AuthnProvider::getSignedInUser(),$pl->__GET("id"));
        $_SESSION['playlist'] = serialize($pl);
        $html = <<<END
             <b>Playlist créé en session</b><br>
             <a href="?action=add-track">Ajouter à mes préférences</a>
        END;
        return $html;
    }

}