<?php
namespace iutnc\netvod\render;

use iutnc\netvod\auth\AuthnProvider;
use iutnc\netvod\repository\NetvodRepository;
use iutnc\netvod\video\Serie;

class SerieRenderer implements Renderer {

    protected Serie $serie;

    public function __construct(Serie $serie) {
        // on garde la référence à la série à afficher
        $this->serie = $serie;
    }

    public function render(int $selecteur): string {
        $affichage = "<div class='serie'>";
        $affichage .= $this->renderHeader();

        if ($selecteur === self::COMMENTAIRES) {
            // si le mode est "commentaires", on affiche juste ça
            $affichage .= $this->renderCommentaires();
        }
        else {
            // sinon, on assemble les différents blocs de la série
            $affichage .= $this->renderImage($selecteur);
            if ($selecteur === self::LONG) {
                $affichage .= $this->renderInfos();
            }
            $affichage .= $this->renderFavorisForm($selecteur);

            // on ajoute les épisodes uniquement en mode LONG
            if ($selecteur === Renderer::LONG) {
                $affichage .= $this->renderEpisodes();
            }
        }

        $affichage .= "</div>";
        return $affichage;
    }

    // Titre et année de la série
    private function renderHeader(): string {
        return "<h2>" . htmlspecialchars($this->serie->__get("title")) .
            " (" . $this->serie->__get("annee") . ")</h2>";
    }

    // Image de la série et lien selon le mode choisi
    private function renderImage(int $selecteur): string {
        $cheminImg = htmlspecialchars($this->serie->__get("cheminImage"));
        $idSerie = $this->serie->__get("id");

        if ($selecteur === Renderer::LONG) {
            // image seule en mode LONG
            return "<img src='image\\{$cheminImg}' alt='Image de la série' width='200'>";
        } elseif ($selecteur === Renderer::SERIERECENTE) {
            // image cliquable vers le prochain épisode ou la série
            $repo = NetvodRepository::getInstance();
            $id_user = AuthnProvider::getSignedInUser()->__GET("id");
            $episode = $repo->getProchainEpisodeEnCours($id_user, $idSerie);

            if ($episode) {
                // lien vers l'épisode en cours
                return "<a href='?action=display-episode&episode={$episode->__get("id")}' >
                            <img src='image\\{$cheminImg}' alt='Image de la série' width='200'>
                        </a>";
            } else {
                // lien vers la série si aucun épisode en cours
                return "<a href='?action=display-serie&id_serie={$idSerie}' >
                            <img src='image\\{$cheminImg}' alt='Image de la série' width='200'>
                        </a>";
            }
        } else {
            // mode par défaut : lien vers la page de la série
            return "<a href='?action=display-serie&id_serie={$idSerie}' >
                        <img src='image\\{$cheminImg}' alt='Image de la série' width='200'>
                    </a>";
        }
    }

    // Informations générales sur la série : genre, public, description
    private function renderInfos(): string {
        $moyenneNotes = htmlspecialchars($this->serie->__get("moyenne"));
        $genre = htmlspecialchars($this->serie->__get("genre"));
        $public = htmlspecialchars($this->serie->__get("typePublic"));
        $desc = htmlspecialchars($this->serie->__get("description"));
        $idSerie = $this->serie->__get("id");

        // informations liées au commentaires de la série
        $commentaires = $this->serie->__get("commentaires");
        $nbCommentaires = count($commentaires);
        if ($nbCommentaires === 0) {
            $nbCommentairesTexte = "Aucun commentaire";
        } elseif ($nbCommentaires === 1) {
            $nbCommentairesTexte = "1 commentaire";
        } else {
            $nbCommentairesTexte = $nbCommentaires . " commentaires";
        }

        if ($moyenneNotes !== null) {
            $moyenneHtml = "<p><strong>Moyenne :</strong> {$moyenneNotes}</p>";
        }
        else {
            $moyenneHtml = "<p><strong>Moyenne :</strong> Pas encore de note</p>";
        }

        $infosHtml = $moyenneHtml .
            "<p><strong>Genre :</strong> {$genre}</p>" .
            "<p><strong>Public :</strong> {$public}</p>" .
            "<p><strong>Description :</strong> {$desc}</p>".
            "<p><strong>Commentaires :</strong> {$nbCommentairesTexte}</p>";

        $lienCommentaires = "<p><strong><a id='titre' href='?action=display-commentaires&id_serie={$idSerie}'>Voir tous les commentaires</a></strong></p>";

        return $infosHtml . $lienCommentaires;
    }

    // Formulaire pour ajouter ou retirer la série des favoris
    private function renderFavorisForm(int $selecteur): string {
        $idSerie = $this->serie->__get("id");

        if (in_array($selecteur, [Renderer::LONG, Renderer::COMPACT, Renderer::SERIERECENTE])) {
            // bouton "Ajouter aux favoris"
            return <<<HTML
                <form action=?action=update-series-pref method=POST>
                    <input type="hidden" name="id_serie" value={$idSerie}>
                    <input type="hidden" name="typeModif" value="ajout">
                    <input type="submit" value="Ajouter aux favoris">
                </form>
            HTML;
        }
        elseif ($selecteur === Renderer::SERIEPREF) {
            // bouton "Retirer des favoris"
            return <<<HTML
                <form action=?action=update-series-pref method=POST>
                    <input type="hidden" name="id_serie" value={$idSerie}>
                    <input type="hidden" name="typeModif" value="retrait">
                    <input type="submit" value="Retirer des favoris">
                </form>
            HTML;
        }

        return "";
    }

    // Affiche tous les épisodes de la série
    private function renderEpisodes(): string {
        $affichage = "";
        $episodes = $this->serie->__get("episodes");

        if (!empty($episodes)) {
            // titre de la section épisodes
           
            // on utilise EpisodeSerieRenderer pour chaque épisode
            foreach ($episodes as $episode) {
                $renderer = new EpisodeSerieRenderer($episode);
                $affichage .= $renderer->render(Renderer::COMPACT);
            }
        }

        return $affichage;
    }

    // Affiche uniquement les commentaires
    private function renderCommentaires(): string {
        $html = "<h3>Commentaires</h3>";
        $comments = $this->serie->__get("commentaires");

        if (!empty($comments)) {
            // chaque commentaire avec l'utilisateur
            foreach ($comments as $c) {
                $email = htmlspecialchars($c['email']);
                $note = htmlspecialchars($c['note']);
                $commentaire = htmlspecialchars($c['commentaire']);

                $html .= <<<HTML
                    <div id="commentaire" class="commentaire">
                        <div class="comment-header">
                            <strong>{$email}</strong>
                            <span class="note">Note : {$note}/5</span>
                        </div>
                        <div class="comment-body">
                            <p>{$commentaire}</p>
                        </div>
                    </div>
                HTML;
            }
        }
        else {
            // pas de commentaire pour cette série
            $html .= "<p>Aucun commentaire pour cette série</p>";
        }

        return $html;
    }

    public static function displayHTMLgenres() : string {
        $repo = NetvodRepository::getInstance();
        $listeGenre = $repo->getGenres();
        $htmlGenre = "<option value = >Aucun</option>";
        foreach($listeGenre as $genre){
            $htmlGenre .= "<option value='$genre'>$genre</option>";
        }
        return $htmlGenre;
    }

    public static function displayHTMLpublics() : string {
        $repo = NetvodRepository::getInstance();
        $listePublic = $repo->getPublics();
        $htmlPublic = "<option value = >Aucun</option>";
        foreach($listePublic as $public){
            $htmlPublic .= "<option value='$public'>$public</option>";
        }
        return $htmlPublic;
    }
}
