<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Middleware{

    require_once __DIR__ . "/../Helper/helper.php";

    use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\TodolistRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
        private TodolistRepository $todolistRepository;

        protected function setUp(): void
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->todolistRepository = new TodolistRepository(Database::getConnection());

            $this->todolistRepository->deleteAll();
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();
            $this->expectOutputRegex("[Location:/users/login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = "anom";
            $user->name = "Anom";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middleware->before();
            $this->expectOutputRegex("[]");

        }
    }
}