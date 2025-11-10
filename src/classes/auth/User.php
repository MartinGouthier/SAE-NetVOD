<?php

namespace iutnc\netvod\auth;

use Exception;

class User
{
    private string $email, $username, $prenom, $nom, $birthday, $genreFav;
    private int $role, $id;

    /**
     * @param string $email
     * @param string $prenom
     * @param string $username
     * @param string $nom
     * @param string $birthday
     * @param string $genreFav
     * @param int $role
     * @param int $id
     */
    public function __construct(string $email, string $prenom, string $username, string $nom, string $birthday, string $genreFav, int $role, int $id)
    {
        $this->email = $email;
        $this->prenom = $prenom;
        $this->username = $username;
        $this->nom = $nom;
        $this->birthday = $birthday;
        $this->genreFav = $genreFav;
        $this->role = $role;
        $this->id = $id;
    }

    public function __get(string $at):mixed {
        if (property_exists ($this, $at)) {
            return $this->$at;
        }
        throw new Exception("$at: invalid property");
    }


}