<?php

namespace app\Controller;

use app\App\View;
use app\Config\Database;
use app\Exception\ValidationException;
use app\Model\UserLoginRequest;
use app\Model\UserProfileUpdateRequest;
use app\Model\UserRegisterRequest;
use app\Repository\SessionRepository;
use app\Repository\UserRepository;
use app\Service\SessionService;
use app\Service\UserService;

class UserController {
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct() {
        $connnection = Database::getConnection();
        $userRepository =  new UserRepository($connnection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connnection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register() {
        View::render('User/register', [
            'title' => 'Register New User'
        ]);
    }

    public function postRegister() {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];


        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $ex) {
            View::render('User/register', [
                'title' => 'Register New User',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function login() {
        View::render('User/login', [
            'title' => 'login User',

        ]);
    }

    public function postLogin() {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];
        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        } catch (ValidationException $ex) {
            View::render('User/login', [
                'title' => 'login User',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function logout() {
        $this->sessionService->destroy();
        View::redirect('/');
    }

    public function updateProfile() {
        $user = $this->sessionService->current();
        View::render('User/profile', [
            'title' => 'Update user profile',
            "userId" => $user->id,
            "name" => $user->name
        ]);
    }

    public function postUpdateProfile() {
        $user = $this->sessionService->current();
        $request = new UserProfileUpdateRequest();
        $request->id =  $user->id;
        $request->name = $_POST['name'];

        try {
            $response = $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $ex) {
            View::render('User/profile', [
                'title' => 'Update user profile',
                "userId" => $user->id,
                "name" => $_POST['name'],
                "error" => $ex->getMessage()
            ]);
            
        }
    }
}
