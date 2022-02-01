<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App{
    function header(string $value)
    {
        echo $value;
    }
}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller {

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\UserController;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

    class UserControllerTest extends TestCase {
        private UserController $userController;
        private UserRepository $userRepository;

        protected function setUp(): void {
            $this->userController = new UserController();
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister() {
            $this->userController->register();
            $this->expectOutputRegex("[register]");
            $this->expectOutputRegex("[id]");
            $this->expectOutputRegex("[name]");
            $this->expectOutputRegex("[password]");
            $this->expectOutputRegex("[Register New User]");
        }

        public function testPostRegisterSucces() {
            $_POST['id'] = 'budi';
            $_POST['name'] = 'budi';
            $_POST['password'] = '123456';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: users/login]");
        }

        public function testPostRegisterValidationError() {
            $_POST['id'] = '';
            $_POST['name'] = 'budi';
            $_POST['password'] = 'budi';

            $this->userController->postRegister();

            $this->expectOutputRegex("[id, name, password cannot blank]");
        }

        public function testPostRegisterDuplicate() {

            $user = new User();

            $user->id = 'budi';
            $user->name = 'budi';
            $user->password = '123456';

            $this->userRepository->save($user);

            $_POST['id'] = 'budi';
            $_POST['name'] = 'budi';
            $_POST['password'] = '123456';

            $this->userController->postRegister();

            $this->expectOutputRegex("[User Already Exist]");
        }
    }
}
