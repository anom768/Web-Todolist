<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Repository;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Todolist;

class TodolistRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Todolist $todolist) : Todolist
    {
        $statement = $this->connection->prepare("INSERT INTO todolist(id, id_user, todolist) VALUES(?,?, ?)");
        $statement->execute([$todolist->id, $todolist->id_user, $todolist->todolist]);
        return $todolist;
    }

    public function generateId(string $id_user) : int
    {
        try {
            $statement = $this->connection->prepare("SELECT MAX(id) AS id FROM todolist WHERE id_user = ?");
            $statement->execute([$id_user]);
            $row = $statement->fetch();
            return (int)$row["id"] + 1;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAll(string $id_user) : ?array
    {
        try {
            $statement = $this->connection->prepare("SELECT id, todolist FROM todolist WHERE id_user = ?");
            $statement->execute([$id_user]);

            if ($row = $statement->fetchAll()) {
                return $row;
            }
            return null;
        } finally {
            $statement->closeCursor();
        }
    }

    public function remove(int $id, string $id_user) : void
    {
        $statement = $this->connection->prepare("DELETE FROM todolist WHERE id = ? AND id_user = ?");
        $statement->execute([$id, $id_user]);
    }

    public function deleteAll()
    {
        $this->connection->exec("DELETE FROM todolist");
    }
}