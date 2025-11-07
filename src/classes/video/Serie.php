<?php
namespace iutnc\netvod\video;
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

    private float $moyenne;

    public function __construct(string $title,
                                string $description,
                                string $cheminImage,
                                int $annee ,
                                string $genre,
                                string $typePublic,
                                int $id,
                                array  $episodes = [],
                                float $moyenne) {
        $this->title = $title;
        $this->description = $description;
        $this->cheminImage = $cheminImage;
        $this->annee = $annee;
        $this->genre = $genre;
        $this->typePublic = $typePublic;
        $this->episodes = $episodes;
        $this->id = $id;
        $this->moyenne = $moyenne;
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

    private function chargerEpisodes(): void {
        $this->episodes = NetvodRepository::getInstance()->getEpisodesBySerie($this->id);
    }

    private function chargerCommentaires(): void {
        $this->commentaires = NetvodRepository::getInstance()->getCommentairesBySerie($this->id);
    }
}
