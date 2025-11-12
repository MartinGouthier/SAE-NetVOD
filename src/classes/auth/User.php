<?php

namespace iutnc\netvod\auth;

use Exception;

class User
{
    private string $email;
    private ?string $username, $prenom, $nom, $birthday, $genreFav;
    private int $role, $id;

    /**
     * @param string $email
     * @param int $role
     * @param int $id
     * @param string|null $prenom
     * @param string|null $username
     * @param string|null $nom
     * @param string|null $birthday
     * @param string|null $genreFav
     */
    public function __construct(string $email, int $role, int $id, ?string $prenom = null, ?string $username = null, ?string $nom = null, ?string $birthday = null, ?string $genreFav = null)
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