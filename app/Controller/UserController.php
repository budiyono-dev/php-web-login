<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;

class UserController {
    private UserService $userService;

    public function __construct()
    {
        $connnection = Database::getConnection();
        $userRepository =  new UserRepository($connnection);
        $this->userService = new UserService($userRepository);
    }

    public function register() {
        View::render('User/register',[
            'title'=>'Register New User'
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
            View::render('User/register',[
                'title'=> 'Register New User',
                'error'=> $ex->getMessage()
            ]);
        }

    }

    public function login()
    {
        View::render('User/login',[
            'title'=>'login User',

        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];
        try {
            $this->userService->login($request);
            View::redirect('/');
        } catch (ValidationException $ex) {
            View::render('User/login',[
                'title'=>'login User',
                'error'=> $ex->getMessage()
            ]);
        }
    }
}
