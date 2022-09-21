<?php

require_once __DIR__ . "/../vendor/autoload.php";

use BangkitAnomSedhayu\Belajar\PHP\MVC\App\Router;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Controller\HomeController;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Controller\UserController;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection("prod");
// ke halaman index
Router::add("GET","/",HomeController::class,"index",[]);
Router::add("POST","/",HomeController::class,"index",[]);

// ke halaman register
Router::add("GET","/users/register",UserController::class,"register",[MustNotLoginMiddleware::class]);
Router::add("POST","/users/register",UserController::class,"postRegister",[MustNotLoginMiddleware::class]);

// kehalaman login
Router::add("GET","/users/login",UserController::class,"login",[MustNotLoginMiddleware::class]);
Router::add("POST","/users/login",UserController::class,"postLogin",[MustNotLoginMiddleware::class]);

// kehalaman index dari logout
Router::add("GET","/users/logout",UserController::class,"logout",[MustLoginMiddleware::class]);

// ke halaman ubah profile
Router::add("GET","/users/profile",UserController::class,"updateProfile",[MustLoginMiddleware::class]);
Router::add("POST","/users/profile",UserController::class,"postUpdateProfile",[MustLoginMiddleware::class]);

// ke halaman todolist
Router::add("GET","/users/todolist",UserController::class,"todolist",[MustLoginMiddleware::class]);
Router::add("POST","/users/todolist",UserController::class,"postTodolist",[MustLoginMiddleware::class]);

// ke halaman ubah password
Router::add("GET","/users/password",UserController::class,"updatePassword",[MustLoginMiddleware::class]);
Router::add("POST","/users/password",UserController::class,"postUpdatePassword",[MustLoginMiddleware::class]);

Router::add("POST", "/todolist/remove", UserController::class, "remove", [MustLoginMiddleware::class]);

Router::run();