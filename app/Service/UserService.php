<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use phpDocumentor\Reflection\Types\This;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
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

            if($user != null){
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
        if ($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("id, name, password cannot blank");
        }
    }
}
