<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

require_once __DIR__ . "/../Helper/helper.php";

use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private SessionService $sessionService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository,$this->userRepository);
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User;
        $user->id = "bangkit";
        $user->name = "Bangkit";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("bangkit");

        $this->expectOutputRegex("[X-PZN-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        
        self::assertEquals("bangkit",$result->id_user);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->id_user = "bangkit";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->id_user = "bangkit";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();
        self::assertEquals($session->id_user,$user->id);
    }
}