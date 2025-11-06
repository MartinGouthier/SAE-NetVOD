<?php
namespace iutnc\netvod\action;

use iutnc\netvod\exception\TokenException;
use iutnc\netvod\repository\NetvodRepository;

class UserActivation extends Action {

    
    public function GET(): String {
       
       
        try { 
            
        if (!isset($_GET['token'])) {
            throw new TokenException("Token error : token manquant");
        }

        $token = $_GET['token'];
    
        $repo = NetvodRepository::getInstance();
        $repo->activationCompte($token);  

        } catch (TokenException $e) {

            return "<p>Erreur : " . $e->getMessage() . "</p";
        }

        return "<p>Votre compte est maintenant activé ! Vous pouvez vous connecter</p>";
        
    }

    public function POST(): String {
        
        return $html = $this->GET();

    }
}