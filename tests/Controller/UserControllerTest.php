<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Controller{

    require_once __DIR__ . "/../Helper/helper.php";

    use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Session;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\TodolistRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\SessionService;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\TodolistService;
    use PHPUnit\Framework\TestCase;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserTodolistRequest;
    use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Todolist;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
        private TodolistRepository $todolistRepository;
        private TodolistService $todolistService;

        protected function setUp(): void
        {
            $this->userController = new UserController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->todolistRepository = new TodolistRepository(Database::getConnection());
            $this->todolistService = new TodolistService($this->todolistRepository);

            $this->todolistRepository->deleteAll();
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        //view register
        public function testRegisterSuccess()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Register new user]");
        }

        public function testPostRegisterValidationError()
        {
            $_POST["id"] = "";
            $_POST["name"] = "Anom";
            $_POST["password"] = "rahasia";
            $_POST["password2"] = "rahasia";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Register new user]");
            $this->expectOutputRegex("[Id, Name, and Password can not BLANK]");
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = "rahasia";

            $this->userRepository->save($user);

            $_POST["id"] = "bangkit";
            $_POST["name"] = "Anom";
            $_POST["password"] = "rahasia";
            $_POST["password2"] = "rahasia";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Register new user]");
            $this->expectOutputRegex("[User id $user->id already exist]");
        }

        public function testPostRegisterDiffPassword()
        {
            $_POST["id"] = "bangkit";
            $_POST["name"] = "Anom";
            $_POST["password"] = "rahasia";
            $_POST["password2"] = "irahasa";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Password not same]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST["id"] = "bangkit";
            $_POST["name"] = "Anom";
            $_POST["password"] = "rahasia";
            $_POST["password2"] = "rahasia";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location:/users/login]");
        }

        public function testLogin()
        {
            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Sign On]");
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST["id"] = "bangkit";
            $_POST["password"] = "rahasia";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location:/]");
        }

        public function testLoginValidationError()
        {
            $_POST["id"] = "";
            $_POST["password"] = "";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Id and Password can not BLANK]");
        }

        public function testLoginUserNotFound()
        {
            $_POST["id"] = "notfound";
            $_POST["password"] = "notfound";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLoginUserWrongPassword()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST["id"] = "bangkit";
            $_POST["password"] = "salah";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location:/]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Bangkit]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["name"] = "Anom";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location:/]");

            $result = $this->userRepository->findById($user->id);

            self::assertEquals($_POST["name"],$result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["name"] = "";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Name can not BLANK]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["oldPassword"] = "rahasia";
            $_POST["newPassword"] = "bangkit";
            $_POST["newPassword2"] = "bangkit";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location:/]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("bangkit",$result->password));
        }

        public function testPostUpdateValidationError()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["oldPassword"] = "";
            $_POST["newPassword"] = "";
            $_POST["newPassword2"] = "";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[Password can not BLANK]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["oldPassword"] = "salah";
            $_POST["newPassword"] = "benar";
            $_POST["newPassword2"] = "benar";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[Old password is wrong]");
        }

        public function testPostUpdatePasswordSameOldPassword()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["oldPassword"] = "rahasia";
            $_POST["newPassword"] = "rahasia";
            $_POST["newPassword2"] = "rahasia";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[Can not same with last password]");
        }

        public function testPostUpdatePasswordDiffNewPassword()
        {
            $user = new User();
            $user->id = "bangkit";
            $user->name = "Bangkit";
            $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["oldPassword"] = "rahasia";
            $_POST["newPassword"] = "new";
            $_POST["newPassword2"] = "wen";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bangkit]");
            $this->expectOutputRegex("[New password not same]");
        }

        public function testTodolist()
        {
            $user = new User();
            $user->id = "anom";
            $user->name = "Anom";
            $user->password = password_hash("anom", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $request = new UserTodolistRequest();
            $request->id = $this->todolistRepository->generateId($user->id);
            $request->id_user = $user->id;
            $request->todolist = "Bersama";
            $this->todolistService->add($request);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->todolist();

            $this->expectOutputRegex("[Todolist]");
            $this->expectOutputRegex("[Bersama]");
        }

        public function testRemove()
        {
            $user = new User();
            $user->id = "anom";
            $user->name = "Anom";
            $user->password = password_hash("anom", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->id_user = $user->id;
            $this->sessionRepository->save($session);

            $todolist = new Todolist();
            $todolist->id = 1;
            $todolist->id_user = $user->id;
            $todolist->todolist = "apa";
            $this->todolistRepository->save($todolist);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_GET["id"] = 1;
            $this->userController->remove();

            $this->expectOutputRegex("[Location:/users/todolist]");
        }
    }
}