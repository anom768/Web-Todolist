<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Exception\ValidationException;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserRegisterRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserRegisterResponse;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\User;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserLoginRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserLoginResponse;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\UserRepository;
use Exception;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request):UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);

            if ($user != null) {
                throw new ValidationException("User id $request->id already exist");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password,PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();

            return $response;
        } catch (Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || $request->password2 == null ||
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "" || trim($request->password2) == "") {
                throw new ValidationException("Id, Name, and Password can not BLANK");
            }
        
        if ($request->password != $request->password2) {
            throw new ValidationException("Password not same");
        }
    }

    public function login(UserLoginRequest $request):UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password is wrong");
        }
        if (password_verify($request->password,$user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;

            return $response;
        } else {
            throw new ValidationException("Id or password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null ||
            trim($request->id) == "" || trim($request->password) == null) {
                throw new ValidationException("Id and Password can not BLANK");
            }
    }

    public function updateProfile(UserProfileUpdateRequest $request):UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new Exception("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);
            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }

    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null ||
            trim($request->id) == "" || trim($request->name) == null) {
                throw new ValidationException("Name can not BLANK");
            }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            if (!password_verify($request->oldPassword,$user->password)) {
                throw new ValidationException("Old password is wrong");
            }

            if (password_verify($request->newPassword,$user->password)) {
                throw new ValidationException("Can not same with last password");
            }

            $user->password = password_hash($request->newPassword,PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null || $request->newPassword2 == null ||
            trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "" || trim($request->newPassword2) == "") {
                throw new ValidationException("Password can not BLANK");
            }
        if ($request->newPassword != $request->newPassword2) {
            throw new ValidationException("New password not same");
        }
    }
}