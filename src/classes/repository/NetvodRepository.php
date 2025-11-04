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
}