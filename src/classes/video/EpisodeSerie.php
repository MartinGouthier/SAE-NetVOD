<?php
namespace iutnc\netvod\video;
class EpisodeSerie {
    private int $numero;
    private string $titre;
    private string $resume;
    private int $duree;
    private string $cheminFichier;
    private int $id;
    private int $id_serie;
    public function __construct(int $numero, string $titre, string $resume, int $duree, string $cheminFichier,int $id, int $id_serie) {
        $this->numero = $numero;
        $this->titre = $titre;
        $this->resume = $resume;
        $this->duree = $duree;
        $this->cheminFichier = $cheminFichier;
        $this->id = $id;
        $this->id_serie = $id_serie;
    }

    public function __get(string $at):mixed {
        if (property_exists ($this, $at)) {
            return $this->$at;
        }
        throw new \Exception ("$at: invalid property");
    }
}