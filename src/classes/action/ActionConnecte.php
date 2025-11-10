<?php

namespace iutnc\netvod\action;

use Exception;
use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\auth\User;
use iutnc\netvod\exception\AuthException;

abstract class ActionConnecte extends Action
{
    protected ?User $user;

    public function execute() : string {
        try {
            // Si l'utiilisateur est connecté, aucune erreur n'est levée
            $this->user = AuthnProvider::getSignedInUser();
            return parent::execute();
        } catch (AuthException $e) {
            return "<p>Vous n'êtes pas connecté</p>"; 
        }
    }

    public function __get(string $at):mixed {
        if (property_exists ($this, $at)) {
            return $this->$at;
        }
        throw new Exception ("$at: invalid property");
    }
}