<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Repository;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Todolist;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;

class TodolistRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private TodolistRepository $todolistRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->todolistRepository = new TodolistRepository(Database::getConnection());
        
        $this->todolistRepository->deleteAll();
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGenerateId()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = "anom";
        $this->userRepository->save($user);

        $id = $this->todolistRepository->generateId($user->id);

        self::assertEquals(1, $id);
    }

    public function testSave()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = password_hash("anom", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $todolist = new Todolist();
        $todolist->id = $this->todolistRepository->generateId($user->id);
        $todolist->id_user = $user->id;
        $todolist->todolist = "Belajar PHP Dasar";
        $result = $this->todolistRepository->save($todolist);

        self::assertEquals($todolist->id, $result->id);
        self::assertEquals($todolist->todolist, $result->todolist);
    }

    public function testFindAllEmpty()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = password_hash("anom", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $result = $this->todolistRepository->findAll($user->id);

        self::assertNull($result);
    }

    public function testFindAllSuccess()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = password_hash("anom", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $todolist1 = new Todolist();
        $todolist1->id = $this->todolistRepository->generateId($user->id);
        $todolist1->id_user = $user->id;
        $todolist1->todolist = "Belajar PHP Dasar";
        $this->todolistRepository->save($todolist1);

        $todolist2 = new Todolist();
        $todolist2->id = $this->todolistRepository->generateId($user->id);
        $todolist2->id_user = $user->id;
        $todolist2->todolist = "Belajar PHP OOP";
        $this->todolistRepository->save($todolist2);

        $todolist3 = new Todolist();
        $todolist3->id = $this->todolistRepository->generateId($user->id);
        $todolist3->id_user = $user->id;
        $todolist3->todolist = "Belajar PHP Database";
        $this->todolistRepository->save($todolist3);

        $result = $this->todolistRepository->findAll($user->id);
        self::assertNotNull($result);
    }

    public function testRemove()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = password_hash("anom", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $todolist = new Todolist();
        $todolist->id = $this->todolistRepository->generateId($user->id);
        $todolist->id_user = $user->id;
        $todolist->todolist = "Belajar PHP Dasar";
        $result = $this->todolistRepository->save($todolist);

        $this->todolistRepository->remove(1, $user->id);

        $result = $this->todolistRepository->findAll($user->id);
        self::assertNull($result);
    }
}