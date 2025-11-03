<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\exception\AuthException;

abstract class ActionConnecte extends Action
{
    public function execute() : string {
        try {
            // Si l'utiilisateur est connecté, aucune erreur n'est levée
            AuthnProvider::getSignedInUser();
            return parent::execute();
        } catch (AuthException $e) {
            return $e->getMessage();
        }
    }
}