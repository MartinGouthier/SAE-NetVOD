<?php

namespace iutnc\netvod\repository;

use iutnc\netvod\action\CatalogueAction;
use iutnc\netvod\exception\DatabaseConnectionException;
use iutnc\netvod\video\EpisodeSerie;
use iutnc\netvod\video\Serie;
use iutnc\netvod\exception\TokenException;

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

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new DatabaseConnectionException("Erreur : impossible de lire le fichier de configuration");
        }
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}";
        self::$config = ['dsn' => $dsn, 'user' => $conf['username'], 'pass' => $conf['password']];
        try {
            $connexionBdd = new NetvodRepository(self::$config);
        } catch (\PDOException) {
            throw new DatabaseConnectionException("Erreur : connexion à la base de données refusée");
        }

    }

    public function getUserInfo(string $email) : array | false
    {
        $requete = "SELECT passwd, username, first_name, last_name, birthday, favorite_genre, role, id FROM user WHERE email = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1, $email);
        $statm->execute();
        return $statm->fetch();
    }
    public function registerNewUser(string $email, string $passwd) : string {
        // supprime les comptes utilisateurs non activés avec un token expiré 
        $requete = "DELETE FROM user WHERE token_expire IS NOT NULL AND token_expire < NOW() AND role != 1;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute();
        
        $requete = "INSERT INTO user (email, passwd, role, token, token_expire) values (?, ?, 0, ?, ?);";
       
        $token = bin2hex(random_bytes(12));
        $expire = date('Y-m-d H:i:s', time() + 3600); // expire dans 1h

        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1, $email);
        $statm->bindParam(2, $passwd);
        $statm->bindParam(3, $token);
        $statm->bindParam(4, $expire);
        $statm->execute();

        return $token;
    }

    public function activationCompte(string $token): void {
       
        $stmt = "SELECT * FROM user WHERE token = ? ";
        $stmt = $this->pdo->prepare($stmt); 
        $stmt->bindParam(1,$token);
        $stmt->execute();
        $user = $stmt->fetch();

        

        if (!$user) {
            throw new TokenException("Token error : token invalide ou expiré");
        }

        // Vérifier que le compte n'est pas déjà actif
        if ($user['role'] !== 0){
            throw new TokenException("Token error : compte déjà activé");
        }

        // Vérifier l’expiration
        if (strtotime($user['token_expire']) < time()) {
             throw new TokenException("Token error : token expiré");
        }

        // Activer le compte
        $stmt = $this->pdo->prepare("UPDATE user SET role = 1, token = NULL, token_expire = NULL WHERE id = ?");   
        $stmt->bindParam(1,$user['id']);
        $stmt->execute();

    }

    // Methode pour verifié si il y a un token et que le compte est déjà activé
   

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
    
    public function getSerieById(int $idSerie) : Serie|false {
        $requete = "SELECT * FROM serie WHERE id = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$idSerie]);
        $donnee = $statm->fetch();
        if (!$donnee)
            return false;
        $moyenneres = $this->getMoyenne($idSerie);
        $moyenne = null;
        if ((int)$moyenneres[1] > 0) {
            $moyenne = (float)$moyenneres[0];
        }
        return new Serie($donnee[1],$donnee[2],$donnee[3],$donnee[4],$donnee[5],$donnee[6],$donnee[7],$donnee[0], $moyenne);
    }
    public function getEpisodesBySerie(int $idSerie): array {
        $requete = "SELECT * FROM episode WHERE serie_id = ? ORDER BY numero ASC;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$idSerie]);

        $episodes = [];
        while ($donnee = $statm->fetch()) {
            $episode = new EpisodeSerie(
                $donnee['numero'],
                $donnee['titre'],
                $donnee['resume'],
                $donnee['duree'],
                $donnee['file'],
                $donnee['id'],
                $donnee['serie_id']
            );
            $episodes[] = $episode;
        }
        return $episodes;
    }

    public function getCommentairesBySerie(int $idSerie): array {
        $requete = "
                    SELECT user.email, notation.note, notation.commentaire
                    FROM notation
                    INNER JOIN user ON user.id = notation.id_user
                    WHERE notation.id_serie = ?
                ";

        $statm = $this->pdo->prepare($requete);
        $statm->execute([$idSerie]);

        $commentaires = [];
        while ($donnee = $statm->fetch(PDO::FETCH_ASSOC)) {
            $commentaires[] = ['email' => $donnee['email'], 'note' => $donnee['note'], 'commentaire' => $donnee['commentaire']];
        }
        return $commentaires;
    }


    public function getEpisodeById(int $idEpisode) : EpisodeSerie|false{
        $requete = "SELECT * FROM episode WHERE id = ?";
        $statm = $this->pdo->prepare($requete);
        $statm->execute(["$idEpisode"]);
        $donnee = $statm->fetch();
        if (!$donnee){
            return false;
        }
        return new EpisodeSerie($donnee[1],$donnee[2],$donnee[3],$donnee[4],$donnee[5],$donnee[0],$donnee[6]);
    }

    public function notePresente(int $id_user, int $id_serie) : bool{
        $requete = "SELECT count(*) FROM notation WHERE id_serie = ? AND id_user = ?";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
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


    public function getSeriesTriees(int $typeTri) : array {
        $tri = match($typeTri){
            CatalogueAction::TRITITRE => "serie.titre",
            CatalogueAction::TRIDATEAJOUT => "date_ajout",
            CatalogueAction::TRINBREPISODE => "nbr",
            CatalogueAction::TRIMOYENNE => "moyenne",
            default => "id"
        };
        $requete = <<<SQL
                     SELECT serie.id, count(distinct episode.id) as nbr, avg(note) as moyenne FROM serie 
                     INNER JOIN episode ON episode.serie_id = serie.id 
                     LEFT JOIN notation ON notation.id_serie = serie.id
                     GROUP BY serie.id
                     ORDER BY $tri
    SQL;
        echo ($tri);
        if ($tri === "nbr" || $tri === "moyenne") {
            echo($tri);
            $requete .= " DESC;";
        }
        $statm = $this->pdo->query($requete);
        $tab = [];
        while ($donnee = $statm->fetch()){
            if (isset($donnee[0]))
                $tab[] = $donnee[0];
        }
        return $tab;
    }

    public function getSeriesFiltrees(array $idSeries, int $typeFiltre, string $filtre = "") : array{
        $typeFiltre = match($typeFiltre){
            CatalogueAction::FILTREMOTCLE => "motcle",
            CatalogueAction::FILTREGENRE => "genre",
            CatalogueAction::FILTREPUBLIC => "typePublic",
            default => "none"
        };
        if ($typeFiltre === "none")
            return $idSeries;
        $tab = [];
        if ($typeFiltre === "genre" || $typeFiltre === "typePublic"){
            foreach ($idSeries as $id){
                $requete = "SELECT id FROM serie WHERE id = $id AND $typeFiltre = ?;";
                $statm = $this->pdo->prepare($requete);
                $statm->execute([$filtre]);
                if ($donnee = $statm->fetch())
                    $tab[] = intval($donnee[0]);
            }
        } else {
            foreach ($idSeries as $id){
                $requete = "SELECT id FROM serie WHERE id = $id AND (titre LIKE ? OR descriptif LIKE ?);";
                $statm = $this->pdo->prepare($requete);
                $statm->execute(["%$filtre%","%$filtre%"]);
                if ($donnee = $statm->fetch())
                    $tab[] = intval($donnee[0]);
            }
        }
        return $tab;
    }

    public function updateUserProfile(string $email, string $username, string $first_name, string $last_name, string $birthday, string $genre): void {
        if($username != '') {
            $requete = "UPDATE user SET username = ? WHERE email = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$username, $email]);
        }
        if($first_name != '') {
            $requete = "UPDATE user SET first_name = ? WHERE email = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$first_name, $email]);
        }
        if($last_name != '') {
            $requete = "UPDATE user SET last_name = ? WHERE email = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$last_name, $email]);
        }
        if($birthday != '') {
            $birthday = date('Y-m-d', strtotime($birthday));
            $requete = "UPDATE user SET birthday = ? WHERE email = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$birthday, $email]);
        }
        if($genre != '') {
            $requete = "UPDATE user SET favorite_genre = ? WHERE email = ?;";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$genre, $email]);
        }
    }
    public function registerpassWordToken($email): String{
        // supprime les token expiré 
       $requete = "UPDATE user 
                SET token = NULL, token_expire = NULL 
                WHERE token_expire IS NOT NULL 
                  AND token_expire < NOW() 
                  AND role = 1";
        $statm = $this->pdo->prepare($requete);
        $statm->execute();
        
        $token = bin2hex(random_bytes(12));
        $expire = date('Y-m-d H:i:s', time() + 300); // expire dans 5 minutes

        $requete = "UPDATE user SET token = ?,token_expire = ? WHERE email = ? and role = 1 ;";

        $statm = $this->pdo->prepare($requete);
        $statm->bindParam(1, $token);
        $statm->bindParam(2, $expire);
        $statm->bindParam(3, $email);
        $statm->execute();

        return $token;

    }


    public function registerNewpassWord($password, $token): void {
        
        $stmt = "SELECT * FROM user WHERE token = ? ";
        $stmt = $this->pdo->prepare($stmt); 
        $stmt->bindParam(1,$token);
        $stmt->execute();
        $user = $stmt->fetch();

        
        if (!$user) {
            throw new TokenException("Token error : token invalide ou expiré");
        }

        // Vérifier que le compte est actif
        if ($user['role'] !== 1){
            throw new TokenException("Token error : compte non activé");
        }

        // Vérifier l’expiration
        if (strtotime($user['token_expire']) < time()) {
             throw new TokenException("Token error : token expiré");
        }

        // modifier le mcompte
        $stmt = $this->pdo->prepare("UPDATE user SET passwd = ?, token = NULL, token_expire = NULL WHERE id = ?");   
        $stmt->bindParam(1, $password);
        $stmt->bindParam(2,$user['id']);
        $stmt->execute();
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

    public function addSeriePref(int $id_serie, int $id_user) : void{
        try {
            $requete = "INSERT INTO seriepreferees VALUES (?,?);";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$id_serie, $id_user]);
        } catch (\PDOException){}
    }

    public function retirerSeriePref(int $id_serie, int $id_user): void
    {
        $requete = "DELETE FROM seriepreferees WHERE id_serie = ? AND id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie,$id_user]);
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
        }
    }

    public function updateEpisodeVisionne(int $id_user, int $id_episode) : void
    {
        $requete = "SELECT count(*) FROM episodevisionne WHERE id_user = ? AND id_episode = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user,$id_episode]);
        $n = (int) $statm->fetch()[0];
        if ($n === 0){
            $requete = "INSERT INTO episodevisionne VALUES (?,?);";
            $statm = $this->pdo->prepare($requete);
            $statm->execute([$id_episode,$id_user]);
            $episode = $this->getEpisodeById($id_episode);
            $this->updateSerieEnCours($episode->__get("id_serie"),$id_user);
        }
    }

    public function getSeriesEnCours(int $id_user): array {
        $requete = "SELECT id_serie, etatVisionnage FROM serieEnCours WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        $tab = [];
        while ($donnee = $statm->fetch()) {
            if (intval($donnee[1]) === 0) {
                $serie = $this->getSerieById($donnee['id_serie']);
                $tab[] = $serie;
            }
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

    public function getMoyenne(int $id_serie) : array{
        $requete = "SELECT round(avg(note),2), count(note) FROM notation WHERE id_serie = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie]);
        return $statm->fetch();
    }

    public function getProchainEpisodeEnCours(int $id_user, int $id_serie) : EpisodeSerie|false {
        $requete = <<<SQL
                    SELECT max(numero) FROM episodevisionne
                    INNER JOIN episode ON episode.id = episodevisionne.id_episode
                    WHERE id_user = ? AND serie_id = ?;
                    SQL;
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user,$id_serie]);
        $numNouveauEpisode = intval($statm->fetch()[0]) + 1;
        $requete = "SELECT id FROM episode WHERE serie_id = ? AND numero = $numNouveauEpisode";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_serie]);
        $res = $statm->fetch();
        if (!$res)
            return false;
        return $this->getEpisodeById(intval($res[0]));
    }

    public function estEpisodeVisionne(int $id_user, int $id_episode) : bool {
        $requete = "SELECT count(*) FROM episodevisionne WHERE id_user = ? AND id_episode = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user,$id_episode]);
        return (intval($statm->fetch()[0] === 1));
    }

    public function getGenres() : array {
        $requete = "SELECT distinct genre FROM serie;";
        $statm = $this->pdo->query($requete);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $donnee[0];
        }
        return $tab;
    }

    public function getPublics() : array {
        $requete = "SELECT distinct typePublic FROM serie;";
        $statm = $this->pdo->query($requete);
        $tab = [];
        while ($donnee = $statm->fetch()){
            $tab[] = $donnee[0];
        }
        return $tab;
    }

    public function getNbEpisodesVus(int $id_user) {
        $requete = "SELECT count(*) FROM episodevisionne WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        return $statm->fetch()[0];
    }

    public function getTotalEpisodes() {
        $requete = "SELECT count(*) FROM episode;";
        $statm = $this->pdo->query($requete);
        return $statm->fetch()[0];
    }

    public function getNbCommentairesPostes(int $id_user) {
        $requete = "SELECT count(*) FROM notation WHERE id_user = ?;";
        $statm = $this->pdo->prepare($requete);
        $statm->execute([$id_user]);
        return $statm->fetch()[0];
    }
}