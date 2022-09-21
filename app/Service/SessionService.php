<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;

class SessionService
{
    public static string $COOKIE_NAME = "X-PZN-SESSION";

    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository,UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public function create(string $id_user):Session
    {
        $session = new Session();
        $session->id = uniqid();
        $session->id_user = $id_user;

        $this->sessionRepository->save($session);

        setcookie(self::$COOKIE_NAME,$session->id,time() + (60 * 60 * 24 * 30),"/");

        return $session;
    }

    public function destroy()
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? "";
        $this->sessionRepository->deleleteByID($sessionId);

        setcookie(self::$COOKIE_NAME,"",1,"/");
    }

    public function current(): ?User
    {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? "";

        $session = $this->sessionRepository->findById($sessionId);
        if($session == null) {
            return null;
        }
        return $this->userRepository->findById($session->id_user);
    }
}