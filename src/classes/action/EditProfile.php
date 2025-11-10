<?php

namespace iutnc\netvod\action;

use iutnc\netvod\repository\NetvodRepository;

class EditProfile extends ActionConnecte {

    public function GET(): string {
        $html = "<h2>Modifier le profil</h2>";
        $html .= "<form method='POST' action='?action=edit-profile'>";
        $html .= "<label for='username'>Nom d'utilisateur :</label><br>";
        $html .= "<input type='text' id='username' name='username'><br><br>";
        $html .= "<label for='first_name'>Prénom :</label><br>";
        $html .= "<input type='text' id='first_name' name='first_name'><br><br>";
        $html .= "<label for='last_name'>Nom :</label><br>";
        $html .= "<input type='text' id='last_name' name='last_name'><br><br>";
        $html .= "<label for='birthday'>Date de naissance :</label><br>";
        $html .= "<input type='date' id='birthday' name='birthday'><br><br>";
        $html .= "<label for='favorite_genre'>Genre préféré :</label><br>";
        $html .= "<select type='action' id='favorite_genre' name='favorite_genre' list='genres'>";
        $html .= "<option value='Thriller'>Thriller</option>";
        $html .= "<option value='Sport'>Sport</option>";
        $html .= "<option value='Comédie'>Comédie</option>";
        $html .= "<option value='Documentaire'>Documentaire</option>";
        $html .= "</select><br><br>";
        $html .= "<input type='submit' value='Enregistrer les modifications'>";
        $html .= "</form>";
        return $html;
    }

    public function POST(): string {
        // Récupération des données du formulaire
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $birthday = $_POST['birthday'];
        $favorite_genre = $_POST['favorite_genre'];

        // Mise à jour des informations dans la base de données
        $repo = NetvodRepository::getInstance();
        $repo->updateUserProfile($_SESSION['user'], $username, $first_name, $last_name, $birthday, $favorite_genre);

        return "<p>Profil mis à jour avec succès !</p><a href='?action=user-profile'>Retour au profil</a>";
    }
}