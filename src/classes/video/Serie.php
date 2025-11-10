<?php
namespace iutnc\netvod\video;
use Exception;
use iutnc\netvod\repository\NetvodRepository;

class Serie {
    private string $title;
    private string $description;
    private string $cheminImage;
    private int $annee;
    private string $genre;
    private string $typePublic;
    private array $episodes = [];
    private array $commentaires = [];
    private int $id;

    private string $dateAjout;

    private ?float $moyenne;

    public function __construct(string $title,
                                string $description,
                                string $cheminImage,
                                int $annee ,
                                string $dateAjout,
                                string $genre,
                                string $typePublic,
                                int $id,
                                ?float $moyenne) {
        $this->title = $title;
        $this->description = $description;
        $this->cheminImage = $cheminImage;
        $this->annee = $annee;
        $this->genre = $genre;
        $this->typePublic = $typePublic;
        $this->id = $id;
        $this->moyenne = $moyenne;
        $this->dateAjout = $dateAjout;
    }


    public function __get(string $at): mixed {
        if ($at === 'episodes') {
            if (empty($this->episodes)) {
                $this->chargerEpisodes();
            }
            return $this->episodes;
        }

        if ($at === 'commentaires') {
            if (empty($this->commentaires)) {
                $this->chargerCommentaires();
            }
            return $this->commentaires;
        }

        if (property_exists($this, $at)) {
            return $this->$at;
        }

        throw new Exception("$at: invalid property");
    }

    public function ajouterEpisode(EpisodeSerie $episode) : void{
        $this->episodes[] = $episode;
    }

    private function chargerEpisodes(): void {
        $this->episodes = NetvodRepository::getInstance()->getEpisodesBySerie($this->id);
    }

    private function chargerCommentaires(): void {
        $this->commentaires = NetvodRepository::getInstance()->getCommentairesBySerie($this->id);
    }
}
