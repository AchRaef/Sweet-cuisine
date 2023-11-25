<?php

namespace app;


use app\models\Utilisateur;
use app\models\Recette;
use PDO;
use PDOException;

/**
 * Class database
 * :package app
 */
class database
{
    public \PDO $connection;
    public static database $database;

    // Connect with MS SQL SERVER
    public function __construct()
    {
        $servername = "localhost"; //INSERER SERVER NAME
        $database = "cuisine";
        $user = "admin";
        $password = "recette1234";

        try {
            $this->connection = new PDO("sqlsrv:Server=$servername;database=$database", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error connecting to SQL Server: " . $e->getMessage());
        }

        self::$database = $this;
    }
    //-------------------- recette CRUD --------------------
    // recette selection
    public function getrecetteLimited($search = '', $startrow, $endrow)
    {
        if ($search) {
            $statement = $this->connection->prepare(
                'SELECT * FROM (select (select count(r.id) from recette as r
                join utilisateur as u
                on r.proprietaire = u.util_id
                where r.nom like :nom
                or u.utilis_nom like :utilis_nom) as TotalNb, Row_Number() over(order by r.id) as RowNum, r.*, u.utilis_nom 
                )T where T.RowNum between :startrow and :endrow;'
            );
            $statement->bindValue(':nom', "%$search%");
            $statement->bindValue(':utilis_nom', "%$search%");
            $statement->bindValue(':startrow', "$startrow");
            $statement->bindValue(':endrow', "$endrow");
        } else {
            $statement = $this->connection->prepare(
                'SELECT * FROM (select (select count(p.id) from recette as r) as TotalNb, Row_Number() over(order by r.id) as RowNum, r.*, u.utilis_nom
                from recette as r
                JOIN utilisateur AS u
                ON r.proprietaire = u.util_id
                )T where T.RowNum between :startrow and :endrow
                order by T.date_creation desc;'
            );
            $statement->bindValue(':startrow', "$startrow");
            $statement->bindValue(':endrow', "$endrow");
        }
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    // recette selection by ID
    public function getRecetteById($id)
    {
        $statement = $this->connection->prepare(
            'SELECT r.*, u.utilis_nom
            FROM recette AS r
            JOIN utilisateur AS u
            ON r.proprietaire = u.util_i
            WHERE r.recette_id = :id'
        );
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    // recette creation
    public function createrecette(Recette $recette)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO Rrecette (recette_id, nom, ingred, etapes, duree, img, proprietaire, date_creation) 
             VALUES (:recette_id, :nom, :ingred, :etapes, :duree, :img, :proprietaire, :date_creation)"
        );
        $statement->bindValue(':recette_id', $recette->id);
        $statement->bindValue(':nom', $recette->nom);
        $statement->bindValue(':ingred', $recette->ingred);
        $statement->bindValue(':etapes', $recette->etapes);
        $statement->bindValue(':duree', $recette->duree);
        $statement->bindValue(':img', $recette->imgPath);
        $statement->bindValue(':proprietaire', $recette->proprietaire);
        $statement->bindValue(':date_creation', date('Y-m-d H:i:s'));
        $statement->execute();
    }
    // recette delete
    public function deleteRecette($id)
    {
        $statement = $this->connection->prepare(
            'DELETE FROM recette WHERE recette_id = :id'
        );
        $statement->bindValue(":id", $id);
        $statement->execute();
    }
    //  recette update
    public function updaterecette(Recette $recette)
    {
        $statement = $this->connection->prepare(
            "UPDATE recette SET 
             nom = :nom, ingred = :ingred, 
             etapes = :etapes, duree = :duree, 
             img = :img, WHERE recette_id = :id"
        );
        $statement->bindValue(':nom', $recette->nom);
        $statement->bindValue(':ingred', $recette->ingred);
        $statement->bindValue(':etapes', $recette->etapes);
        $statement->bindValue(':duree', $recette->duree);
        $statement->bindValue(':img', $recette->imgPath);
        $statement->bindValue(':id', $recette->id);
        $statement->execute();
    }
    //-------------------- utilisateur CRUD --------------------
    // utilisateur selection
    public function getUtilisateur($search = '')
    {
        if ($search) {
            $statement = $this->connection->prepare(
                'SELECT *
                From utilisateur 
                where utili_nom LIKE :utili_nom
                '
            );
        } else {
            $statement = $this->connection->prepare(
                'SELECT *
                From utilisateur 
                '
            );
        }
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
        // utilisateur selection by ID
        public function getutilisateurById($id)
        {
            $statement = $this->connection->prepare(
                'SELECT *
                From utilisateur 
                WHERE util_id = :id'
            );
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        }
         // utilisateur creation
    public function createUtilisateur(utilisateur $utilisateur)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO utilisateur (utili_nom, password, email,) 
            VALUES (:utili_nom, :password, :email)"
        );
        $statement->bindValue(':utili_nom', $utilisateur->utili_nom);
        $statement->bindValue(':password', $utilisateur->password);
        $statement->bindValue(':email', $utilisateur->email);
        $statement->execute();
    }
    // utilisateur update
    public function updateUtilisateur(Utilisateur $utilisateur)
    {
        $statement = $this->connection->prepare(
            "UPDATE utilisateur SET 
            utili_nom = :utili_nom, password = :password, email = :email
            WHERE util_id = :id"
        );
        $statement->bindValue(':utili_nom', $utilisateur->utili_nom);
        $statement->bindValue(':password', $utilisateur->password);
        $statement->bindValue(':email', $utilisateur->email);
        $statement->bindValue(':id', $utilisateur->id);
        $statement->execute();
    }
}
