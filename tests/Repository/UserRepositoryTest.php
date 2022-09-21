<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Repository;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private TodolistRepository $todolistRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->todolistRepository = new TodolistRepository(Database::getConnection());

        $this->todolistRepository->deleteAll();
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "anom";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id,$result->id);
        self::assertEquals($user->name,$result->name);
        self::assertEquals($user->password,$result->password);
    }

    public function testFIndByIdNotFound()
    {
        $user = $this->userRepository->findById("not found");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "anom";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $user->name = "Bangkit";
        $result = $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id,$result->id);
        self::assertEquals($user->name,$result->name);
        self::assertEquals($user->password,$result->password);

    }
}