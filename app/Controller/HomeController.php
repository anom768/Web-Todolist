<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Controller;

use BangkitAnomSedhayu\Belajar\PHP\MVC\App\View;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository,$userRepository);
    }

    function index()
    {
        $user = $this->sessionService->current();
        if($user == null) {
            View::render("Home/index", [
                "title" => "PHP Login Management"
            ]);
        } else {
            View::render("Home/dashboard", [
                "title" => "Dashboard",
                "user" => [
                    "name" => $user->name
                ]
            ]);
        }
    }
}