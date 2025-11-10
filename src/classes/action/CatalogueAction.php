<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\repository\NetvodRepository;


class CatalogueAction extends ActionConnecte {

    const FILTREGENRE = 1;
    const FILTREPUBLIC = 2;
    const FILTREMOTCLE = 3;

    const AUCUNTRI = 4;
    const TRITITRE = 5;
    const TRIDATEAJOUT = 6;
    const TRINBREPISODE = 7;
    const TRIMOYENNE = 8;

    public function GET(): string {
        $pdo = NetvodRepository::getInstance();
        $tab = $pdo->getSeries();

        $html = $this->displayForm();
        foreach ($tab as $s) {
            $renderer = new SerieRenderer($s);
            $html.= $renderer->render(Renderer::COMPACT);
        }
        
        return $html;
    }

    public function POST(): string {
        $typeTri = intval($_POST['typeTri']);
        $typeFiltre = intval($_POST['typeFiltre']);
        $filtre = $_POST["filtre$typeFiltre"];

        $pdo = NetvodRepository::getInstance();
        $idSeries = $pdo->getSeries();

        $html = $this->displayForm();


        if ($typeTri !== self::AUCUNTRI)
            $idSeries = $pdo->getSeriesTriees($typeTri);

        if (filter_var($filtre, FILTER_SANITIZE_SPECIAL_CHARS)){
            $idSeries = $pdo->getSeriesFiltrees($idSeries, $typeFiltre, $filtre);
        }
        foreach ($idSeries as $s) {
            $serie = $pdo->getSerieById($s);
            $renderer = new SerieRenderer($serie);
            $html .= $renderer->render(Renderer::COMPACT);
        }
        return $html;

    }

    public function displayForm() : string {


        $htmlGenre = SerieRenderer::displayHTMLgenres();
        $htmlPublic = SerieRenderer::displayHTMLpublics();

        return <<<HTML
        <h2>Catalogue des séries</h2>
        <form method="POST" action="?action=catalogue">
          <label for="typeFiltre">Filtrer les séries par:</label>
          <select name="typeFiltre" id="typeFiltre">
            <option value=0>Aucun</option>
            <option value=3>Mot clé</option>
            <option value=1>Genre</option>
            <option value=2>Type de publique</option>
          </select>
          <br>
          
          <div id="formGenre" style="display: none">
           <label for="genreFiltre">Genre :</label>
           <select id="genreFiltre" name="filtre1">
            $htmlGenre
           </select>
          </div>

          <div id="formPublic" style="display: none">
           <label for="publicFiltre">Type de public :</label>
           <select id="publicFiltre" name="filtre2">
            $htmlPublic
           </select>
          </div>
          
          <div id ="formMotCle" style="display: none">
          <label for="motcleFiltre">Filtre</label>
          <input type="text" name="filtre3" id="motcleFiltre">
          </div>
          
          <br>
          
          <label for="typeTri">Trier par :</label>
          <select name="typeTri" id="typeTri">
            <option value=0>Aucun</option>
            <option value=5>Titre</option>
            <option value=6>Date d'ajout</option>
            <option value=7>Nombre d'épisodes</option>
            <option value=8>Moyenne des notes</option>
          </select>       
          
          <input type="submit" value="Appliquer les choix">
        </form>

        <script>
            const select = document.getElementById('typeFiltre');
            const formGenre = document.getElementById('formGenre');
            const formPublic = document.getElementById('formPublic');
            const formMotCle = document.getElementById('formMotCle');

            select.addEventListener('change', function() {
            formGenre.style.display = 'none';
            formPublic.style.display = 'none';
            formMotCle.style.display = 'none';
            
            if (this.value === '3') {
              formMotCle.style.display = 'block';
            } else if (this.value === '1') {
              formGenre.style.display = 'block';
            } else if (this.value === '2') {
                formPublic.style.display = 'block';
            }
          });
        </script>
        HTML;
    }
}