<?php

namespace iutnc\netvod\repository;

use iutnc\netvod\video\EpisodeSerie;
use iutnc\netvod\video\Serie;

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

    public function getSeries() : array {
        $requete = "SELECT id FROM serie;";
        $tab = [];
        $statm = $this->pdo->query($requete);
        $statm->execute();
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
        $serie = new Serie($donnee[1],$donnee[2],$donnee[3],$donnee[4],$donnee[6],$donnee[7]);

        $requete = "SELECT * FROM episode WHERE id_serie = ?;";
        $statm2 = $this->pdo->prepare($requete);
        $statm2->execute([$idSerie]);
        while ($donneeEpisodes = $statm2->fetch()){
            $episode = new EpisodeSerie($donneeEpisodes[1],$donneeEpisodes[2],$donneeEpisodes[3],$donneeEpisodes[4],$donneeEpisodes[5]);
            $serie->ajouterEpisode($episode);
        }
        return $serie;
    }
}