<?php
namespace iutnc\netvod\video;
class EpisodeSerie {
    private int $numero;
    private string $titre;
    private string $resume;
    private int $duree;
    private int $cheminFichier;
    private int $id;
    public function __construct(int $numero, string $titre, string $resume, int $duree, int $cheminFichier,int $id) {
        $this->numero = $numero;
        $this->titre = $titre;
        $this->resume = $resume;
        $this->duree = $duree;
        $this->cheminFichier = $cheminFichier;
        $this->id = $id;
    }

    public function __get(string $at):mixed {
        if (property_exists ($this, $at)) {
            return $this->$at;
        }
        throw new Exception ("$at: invalid property");
    }
}