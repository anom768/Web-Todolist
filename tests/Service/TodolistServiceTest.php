<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Exception\ValidationException;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\TodolistRepository;
use PHPUnit\Framework\TestCase;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserTodolistRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;

class TodolistServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private TodolistRepository $todolistRepository;
    private TodolistService $todolistService;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->todolistRepository = new TodolistRepository(Database::getConnection());
        $this->todolistService = new TodolistService($this->todolistRepository);

        $this->todolistRepository->deleteAll();
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testAddSuccess()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = password_hash("anom", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserTodolistRequest();
        $request->id = $this->todolistRepository->generateId($user->id);
        $request->id_user = $user->id;
        $request->todolist = "Belajar PHP Dasar";
        $result = $this->todolistService->add($request);

        self::assertNotNull($result);
        self::assertEquals($request->id, $result->todolist->id);
        self::assertEquals($request->id_user, $result->todolist->id_user);
        self::assertEquals($request->todolist, $result->todolist->todolist);
    }

    public function testValidateTodolistRequest()
    {
        $this->expectException(ValidationException::class);

        $request = new UserTodolistRequest();
        $request->id = "";
        $request->id_user = "";
        $request->todolist = "";

        $this->todolistService->add($request);
    }
}