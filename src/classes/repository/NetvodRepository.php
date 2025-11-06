<?php

namespace iutnc\netvod\repository;

use iutnc\netvod\video\EpisodeSerie;
use iutnc\netvod\video\Serie;
use PDO;

class NetvodRepository
{
    private PDO $pdo;
    private static ?NetvodRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function getInstance(): NetvodRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new NetvodRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file)
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }

        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}";
        self::$config = ['dsn' => $dsn, 'user' => $conf['username'], 'pass' => $conf['password']];
    }

    public function getUserInfo(string $email) : array
    {
        $requete = "SELECT passwd, role, id FROM user WHERE email = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1, $email);
        $statm->execute();
        return $statm->fetch();
    }
    public function registerNewUser(string $email,string $passwd) : void {
        $requete = "INSERT INTO user (email, passwd, role) values (?, ?, 0);";
        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1,$email);
        $statm->bindParam(2,$passwd);
        $statm->execute();
    }

    public function activationCompte(string $email): void {
        $requete = "UPDATE user SET role = 1 WHERE email = ?";
        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1,$email);
        $statm->execute();
    }

    public function verifieEmailExiste(string $email) : bool {
        $requete = "SELECT count(*) FROM user WHERE email = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1, $email);
        $statm->execute();
        $nbr = $statm->fetch()[0];
        return ((int)$nbr !== 0);
    }

    public function getSeries() : array {
        $requete = "SELECT id FROM serie;";
        $tab = [];
        $statm = $this->pdo->query($requete);
        while ($donnee = $statm->fetch()){
            $serie = $this->getSerieById($donnee[0]);
            $tab[] = $serie;
        }
        return $tab;
    }

    public function getSerieById(int $idSerie) : Serie {
        $requete = "SELECT * FROM serie WHERE id = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$idSerie]);
        $donnee = $statm->fetch();
        $serie = new Serie($donnee[1],$donnee[2],$donnee[3],$donnee[4],$donnee[6],$donnee[7],$donnee[0]);

        $requete = "SELECT * FROM episode WHERE serie_id = ?;";
        $statm2 = $this->pdo->prepare($requete);
        $statm2->execute([$idSerie]);
        while ($donneeEpisodes = $statm2->fetch()){
            $episode = new EpisodeSerie($donneeEpisodes[1],$donneeEpisodes[2],$donneeEpisodes[3],$donneeEpisodes[4],$donneeEpisodes[5],$donneeEpisodes[0],$donneeEpisodes[6]);
            $serie->ajouterEpisode($episode);
        }
        return $serie;
    }

    public function getEpisodeById(int $idEpisode) : EpisodeSerie{
        $requete = "SELECT * FROM episode WHERE id = ?";
        $statm = $this->pdo->prepare($requete);
        $statm->execute(["$idEpisode"]);
        $donnee = $statm->fetch();
        return new EpisodeSerie($donnee[1],$donnee[2],$donnee[3],$donnee[4],$donnee[5],$donnee[0],$donnee[6]);
    }

    public function notePresente(int $id_user, int $id_serie) : bool{
        $requete = "SELECT count(*) FROM notation WHERE id_serie = ? AND id_user = ?";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user,$id_serie]);
        $n = (int) $statm->fetch()[0];
        return ($n === 1);
    }

    public function ajouterNoteEtCommentaire(int $id_user, int $id_serie, int $note, string $commentaire): void
    {
        $requete = "INSERT INTO notation VALUES (?,?,?,?);";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user,$note,$commentaire]);
    }

    public function getMoyennesSeries() : array {
        $requete = "SELECT id_serie, avg(note) FROM notation GROUP BY id_serie ORDER BY avg(note) DESC;";
        $statm = $this->pdo->prepare($requete);
        $tab = [];
        $statm->execute();
        while ($donnee = $statm->fetch()){
            $tab[] = $donnee;
        }
        // Resultat sous forme [[MeilleurNote,id_serie],[2emeNote, id_serie]...]
        return $tab;
    }

    public function getSeriesFiltre(int $typeFiltre, string $filtre) : array {
        // Filtre 1 = Mot Clé
        if ($typeFiltre === 1) {
            //TODO Tester et verifier si LIKE fonctionne
            $requete = "SELECT id FROM serie WHERE titre LIKE '%?%' OR descriptif LIKE '%?%';";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$filtre,$filtre]);
        }else {
            // Filtre 2 = Genre
            if ($typeFiltre === 2)
                $requete = "SELECT id FROM serie WHERE genre = ?;";
            else
                // Filtre 3 = Type de publique
                $requete = "SELECT id FROM serie WHERE typePublic = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$filtre]);
        }
        $tab = [];
        while ($donnee = $statm->fetch()){
            $serie = $this->getSerieById($donnee[0]);
            $tab[] = $serie;
        }
        return $tab;
    }

    public function addSeriePref(int $id_serie,int $id_user) : void{
        $requete = "INSERT INTO seriepreferees VALUES (?,?);";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
    }

    public function getSeriesPref(int $id_user) : array{
        $requete = "SELECT id_serie FROM seriepreferees WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $this->getSerieById($donnee[0]);
        }
        return $tab;
    }

    public function addSeriePref(int $id_serie,int $id_user) : void{
        $requete = "INSERT INTO seriepreferees VALUES (?,?);";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
    }

    public function retirerSeriePref(int $id_serie, int $id_user): void
    {
        $requete = "DELETE FROM seriepreferees WHERE id_serie = ? AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
    }

    public function getSeriesPref(int $id_user) : array{
        $requete = "SELECT id_serie FROM seriepreferees WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $this->getSerieById($donnee[0]);
        }
        return $tab;
    }

    public function updateSerieEnCours(int $id_serie, int $id_user): void {
        $requete = "SELECT COUNT(*) FROM serieEnCours WHERE id_serie = ? AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie, $id_user]);
        $existe = (int) $statm->fetch()[0];
        if ($existe === 0) {
            $requete = "INSERT INTO serieEnCours (id_serie, id_user, etatVisionnage) VALUES (?, ?, 0);";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$id_serie, $id_user]);
        } else {

            $requete = <<<SQL
                            -- Nbr d'épisodes visionnés
                            SELECT count(*) FROM episodevisionne 
                            INNER JOIN episode ON episode.id = episodevisionne.id_episode
                            WHERE serie_id = ? AND id_user = ?
            SQL;
            $statm1 = $this->pdo->prepare($requete);
            $statm1->execute([$id_serie,$id_user]);
            $nbrVisionne = (int) $statm1->fetch()[0];
            $requete = <<<SQL
                        -- Nbr d'épisodes dans la série
                        SELECT count(*) FROM episode
                        WHERE serie_id = ?;
                        SQL;
            $statm2 = $this->pdo->prepare($requete);
            $statm2->execute([$id_serie]);
            $nbrEpisodes = (int) $statm2->fetch()[0];
            if ($nbrEpisodes ===  $nbrVisionne) {
                $requete = "UPDATE serieEnCours SET etatVisionnage = 1 WHERE id_serie = ? AND id_user = ?;";
                $statm3 = $this->pdo->prepare($requete);
                $statm3->execute([$id_serie, $id_user]);
            }
            $requete = "UPDATE serieEnCours SET etatVisionnage = ? WHERE id_serie = ? AND id_user = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$etat, $id_serie, $id_user]);
        }
    }

    public function supSeriePref(int $id_user,int $id_serie) : void{
        $requete = "DELETE FROM seriepreferees WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
    }

    public function getSeriesEnCours(int $id_user): array {
        $requete = "SELECT id_serie, etatVisionnage FROM serieEnCours WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()) {
            $serie = $this->getSerieById($donnee['id_serie']);
            $serie->etatVisionnage = $donnee['etatVisionnage'];
            $tab[] = $serie;
        }
        return $tab;
    }

    public function addSeriePref(int $id_serie,int $id_user) : void{
        $requete = "INSERT INTO seriepreferees VALUES (?,?);";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
    }

    public function retirerSeriePref(int $id_serie, int $id_user): void
    {
        $requete = "DELETE FROM seriepreferees WHERE id_serie = ? AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
    }

    public function getSeriesPref(int $id_user) : array{
        $requete = "SELECT id_serie FROM seriepreferees WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $this->getSerieById($donnee[0]);
        }
        return $tab;
    }

    public function addSerieEnCours(int $id_serie, int $id_user, int $etat = 0): void {
        $requete = "SELECT COUNT(*) FROM serieEnCours WHERE id_serie = ? AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie, $id_user]);
        $existe = (int) $statm->fetch()[0];
        if ($existe === 0) {
            $requete = "INSERT INTO serieEnCours (id_serie, id_user, etatVisionnage) VALUES (?, ?, ?);";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$id_serie, $id_user, $etat]);
        } else {

            $requete = <<<SQL
                            -- Nbr d'épisodes visionnés
                            SELECT count(*) FROM episodevisionne 
                            INNER JOIN episode ON episode.id = episodevisionne.id_episode
                            WHERE serie_id = ? AND id_user = ?
            SQL;
            $statm1 = $this->pdo->prepare($requete);
            $statm1->execute([$id_serie,$id_user]);
            $nbrVisionne = (int) $statm1->fetch()[0];
            $requete = <<<SQL
                        -- Nbr d'épisodes dans la série
                        SELECT count(*) FROM episode
                        WHERE serie_id = ?;
                        SQL;
            $statm2 = $this->pdo->prepare($requete);
            $statm2->execute([$id_serie]);
            $nbrEpisodes = (int) $statm2->fetch()[0];
            if ($nbrEpisodes ===  $nbrVisionne) {
                $requete = "UPDATE serieEnCours SET etatVisionnage = 1 WHERE id_serie = ? AND id_user = ?;";
                $statm3 = $this->pdo->prepare($requete);
                $statm3->execute([$id_serie, $id_user]);
            }
        }
    }

    public function supSeriePref(int $id_user,int $id_serie) : void{
        $requete = "DELETE FROM seriepreferees WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
    }

    public function getSeriesEnCours(int $id_user): array {
        $requete = "SELECT id_serie, etatVisionnage FROM serieEnCours WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()) {
            $serie = $this->getSerieById($donnee['id_serie']);
            $serie->etatVisionnage = $donnee['etatVisionnage']; // pratique pour affichage
            $tab[] = $serie;
        }
        return $tab;
    }

    public function getSeriesTerminees(int $id_user) : array{
        $requete = "SELECT id_serie FROM serieencours WHERE etatVisionnage = 1 AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $this->getSerieById($donnee[0]);
        }
        return $tab;
    }

    public function getSeriesTerminees(int $id_user) : array{
        $requete = "SELECT id_serie FROM serieencours WHERE etatVisionnage = 1 AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $this->getSerieById($donnee[0]);
        }
        return $tab;
    }
}