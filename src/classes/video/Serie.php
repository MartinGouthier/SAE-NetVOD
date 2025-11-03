<?php
namespace iutnc\netvod\video;
class Serie {
    private array $episodes;
    private string $title;
    private string $description;
    private string $cheminImage;
    private int $annee;
    private string $genre;
    private string $typePublic;

    public function __construct(string $title, string $description, string $cheminImage,int $annee ,string $genre, string $typePublic, array  $episodes = []) {
        $this->title = $title;
        $this->description = $description;
        $this->cheminImage = $cheminImage;
        $this->annee = $annee;
        $this->genre = $genre;
        $this->typePublic = $typePublic;
        $this->episodes = $episodes;
    }

    public function __get($name) {
        return $this->$name;
    }

    public function ajouterEpisode(EpisodeSerie $episode) : void{
        $this->episodes[] = $episode;
    }
}
