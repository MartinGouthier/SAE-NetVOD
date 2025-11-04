<?php

namespace iutnc\netvod\repository;

class NetvodRepository
{
    private \PDO $pdo;
    private static ?NetvodRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
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
            $episode = new EpisodeSerie($donneeEpisodes[1],$donneeEpisodes[2],$donneeEpisodes[3],$donneeEpisodes[4],$donneeEpisodes[5],$donneeEpisodes[0]);
            $serie->ajouterEpisode($episode);
        }
        return $serie;
    }

    public function notePresente(int $id_user, int $id_serie) : bool{
        $requete = "SELECT count(*) FROM notation WHERE serie_id = ? AND id_user = ?";
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
            // Mise à jour de l’état (par ex. si la série est terminée)
            $requete = "UPDATE serieEnCours SET etatVisionnage = ? WHERE id_serie = ? AND id_user = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$etat, $id_serie, $id_user]);
        }
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
}