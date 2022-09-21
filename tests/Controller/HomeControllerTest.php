<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Controller;

require_once __DIR__ . "/../Helper/helper.php";

use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function setUp():void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();
        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->id = "sedhayu";
        $user->name = "Sedhayu";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->id_user = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();
        $this->expectOutputRegex("[Hello $user->name]");
    }
}