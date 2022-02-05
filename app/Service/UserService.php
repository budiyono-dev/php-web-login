<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use phpDocumentor\Reflection\Types\This;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse {
        $this->validateUserRegistrationRequest($request);
        try {
            Database::begintransaction();

            $user = $this->userRepository->findById($request->id);

            if ($user != null) {
                throw new ValidationException("User Already Exist");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Throwable $th) {
            Database::rollbackTransaction();
            throw $th;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request) {
        if (
            $request->id == null || $request->name == null || $request->password == null ||
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""
        ) {
            throw new ValidationException("id, name, password cannot blank");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse {
        $this->validateUserLoginRequest($request);
        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Username or password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("id or password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request) {
        if (
            $request->id == null ||  $request->password == null ||
            trim($request->id) == "" ||  trim($request->password) == ""
        ) {
            throw new ValidationException("id, password cannot blank");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::begintransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User Not Found");
            }

            $user->name = $request -> name;
            $this->userRepository->update($user);
            
            Database::commitTransaction();
            $response = new UserProfileUpdateResponse();
            $response->user = $user;

            return $response;
        } catch (\Exception $exe) {
            Database::rollbackTransaction();
            $exe->getMessage();
        }
    }

    public function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request) {
        if (
            $request->id == null || $request->name == null ||
            trim($request->id) == "" || trim($request->name) == ""
        ) {
            throw new ValidationException("id, name cannot blank");
        }
    }
}
