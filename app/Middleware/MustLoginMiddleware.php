<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Middleware;

use BangkitAnomSedhayu\Belajar\PHP\MVC\App\View;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository,$userRepository);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            View::redirect("/users/login");
        }
    }
}