<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Repository;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "bangkit";
        $user->name = "Bangkit";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->id_user = "bangkit";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id,$result->id);
        self::assertEquals($session->id_user,$result->id_user);
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->id_user = "bangkit";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->deleleteByID($session->id);

        $result = $this->sessionRepository->findById($session->id);

        self::assertNull($result);
    }

    public function testByIdNotFound()
    {
        $result = $this->sessionRepository->findById("notfound");

        self::assertNull($result);
    }
}