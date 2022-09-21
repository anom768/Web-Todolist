<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Repository;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }
    public function save(Session $session):Session
    {
        $statement = $this->connection->prepare("INSERT INTO sessions(id,id_user) VALUES(?,?)");
        $statement->execute([$session->id,$session->id_user]);
        return $session;
    }

    public function findById(string $id):?Session
    {
        $statement = $this->connection->prepare("SELECT id,id_user FROM sessions WHERE id = ?");
        $statement->execute([$id]);
        
        try {
            if ($row = $statement->fetch()) {
                $session = new Session();
                $session->id = $row["id"];
                $session->id_user = $row["id_user"];

                return $session;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleleteByID(string $id):void
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $statement->execute([$id]);
    }

    public function deleteAll():void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}