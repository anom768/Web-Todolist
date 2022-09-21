<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserRegisterRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Service\UserService;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Exception\ValidationException;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserLoginRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\SessionRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "anom";
        $request->name = "Anom";
        $request->password = "rahasia";
        $request->password2 = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($request->id,$response->user->id);
        self::assertEquals($request->name,$response->user->name);
        self::assertNotEquals($request->password,$response->user->password);

        self::assertTrue(password_verify($request->password,$response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";
        $request->password2 = "";

        $this->userService->register($request);
    }

    public function testRegisterDucplicate()
    {
        $user = new User();
        $user->id = "anom";
        $user->name = "Anom";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "anom";
        $request->name = "Anom";
        $request->password = "rahasia";
        $request->password2 = "rahasia";

        $this->userService->register($request);
    }

    public function testRegisterDiffPassword()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "anom";
        $request->name = "Anom";
        $request->password = "rahasia";
        $request->password2 = "irahasa";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "anom";
        $request->password = "rahasia";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "aaa";
        $request->password = "salah";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "aaa";
        $request->password = "rahasia";

        $this->userService->login($request);

        self::assertEquals($request->id,$user->id);
        self::assertTrue($request->password,$user->password);
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "aaa";
        $request->name = "bangkit";

        $this->userService->updateProfile($request);
        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "rahasia";
        $request->newPassword = "new";
        $request->newPassword2 = "new";
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword,$result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "aaa";
        $request->oldPassword = "rahasia";
        $request->newPassword = "new";
        $request->newPassword2 = "new";
        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordWrongPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "salah";
        $request->newPassword = "new";
        $request->newPassword2 = "new";
        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "aaa";
        $request->oldPassword = "salah";
        $request->newPassword = "new";
        $request->newPassword2 = "new";
        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordSamePassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "rahasia";
        $request->newPassword = "rahasia";
        $request->newPassword2 = "rahasia";
        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordDiffNewPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "aaa";
        $user->name = "Aaa";
        $user->password = password_hash("rahasia",PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "rahasia";
        $request->newPassword = "new";
        $request->newPassword2 = "wen";
        $this->userService->updatePassword($request);
    }
}