<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Controller;

use BangkitAnomSedhayu\Belajar\PHP\MVC\App\View;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Exception\ValidationException;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserLoginRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserRegisterRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\UserService;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserTodolistRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\TodolistRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\TodolistService;

class UserController
{
    private UserService $userService;
    private SessionService $sesssionService;
    private TodolistRepository $todolistRepository;
    private TodolistService $todolistService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        $this->todolistRepository = new TodolistRepository($connection);
        $this->todolistService = new TodolistService($this->todolistRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sesssionService = new SessionService($sessionRepository,$userRepository);
    }

    public function register()
    {
        View::render("User/register",[
            "title" => "Register new user"
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST["id"];
        $request->name = $_POST["name"];
        $request->password = $_POST["password"];
        $request->password2 = $_POST["password2"];

        try {
            $this->userService->register($request);
            View::redirect("/users/login");
        } catch(ValidationException $exception){
            View::render("User/register",[
                "title" => "Register new user",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render("User/login",[
            "title" => "Login user"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST["id"];
        $request->password = $_POST["password"];

        try {
            $response = $this->userService->login($request);
            $this->sesssionService->create($response->user->id);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("User/login",[
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]);

        }
    }

    public function logout()
    {
        $this->sesssionService->destroy();
        View::redirect("/");
    }

    public function updateProfile()
    {
        $user = $this->sesssionService->current();

        View::render("User/profile",[
            "title" => "Update profile",
            "user" => [
                "id" => $user->id,
                "name" => $user->name
            ]
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sesssionService->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST["name"];

        try {
            $this->userService->updateProfile($request);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("User/profile",[
                "title" => "Update profile",
                "error" => $exception->getMessage(),
                "user" => [
                    "id" => $user->id,
                    "name" => $_POST["name"]
                ]
                ]);
        }
    }

    public function updatePassword()
    {
        $user = $this->sesssionService->current();

        View::render("User/password",[
            "title" => "Update Password",
            "user" => $user->id
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->sesssionService->current();

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST["oldPassword"];
        $request->newPassword = $_POST["newPassword"];
        $request->newPassword2 = $_POST["newPassword2"];
        
        try {
            $this->userService->updatePassword($request);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("User/password",[
                "title" => "Update Password",
                "error" => $exception->getMessage(),
                "user" => $user->id
            ]);
        }
    }

    public function todolist()
    {
        $user = $this->sesssionService->current();
        $todolist = $this->todolistRepository->findAll($user->id);

        View::render("User/todolist", [
            "title" => "Todolist",
            "todolist" => $todolist
        ]);
    }

    public function postTodolist()
    {
        $user = $this->sesssionService->current();
        $todolist = $this->todolistRepository->findAll($user->id);

        $request = new UserTodolistRequest();
        $request->id = $this->todolistRepository->generateId($user->id);
        $request->id_user = $user->id;
        $request->todolist = $_POST["todo"];

        try {
            $this->todolistService->add($request);
            View::redirect("/users/todolist");
        } catch (ValidationException $exception) {
            View::render("User/todolist", [
                "title" => "Todolist",
                "todolist" => $todolist,
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function remove()
    {
        $user = $this->sesssionService->current();

        $this->todolistRepository->remove($_GET["id"], $user->id);

        View::redirect("/users/todolist");
    }
}