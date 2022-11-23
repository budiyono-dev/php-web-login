<?php
namespace test\Service;

use PHPUnit\Framework\TestCase;
use app\Config\Database;
use app\Domain\User;
use app\Exception\ValidationException;
use app\Model\UserLoginRequest;
use app\Model\UserRegisterRequest;
use app\Repository\UserRepository;
use app\Service\UserService;

class UserServiceTest extends TestCase {
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess() {
        $request = new UserRegisterRequest();

        $request->id = "budi1";
        $request->name = "Budyono";
        $request->password = "12456";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed() {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();

        $request->id = "";
        $request->name = "Budyono";
        $request->password = "12456";
        $this->userService->register($request);
    }

    public function testRegisterDuplicate() {
        $user = new User();
        $user->id = "budi2";
        $user->name = "Budiyono";
        $user->password = "12456";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);
        $req = new UserRegisterRequest();
        $req->id = "budi2";
        $req->name = "Budiyono";
        $req->password = "12456";

        $this->userService->register($req);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();

        $request->id = "budi1";
        $request->password = "124561";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->id = "budi2";
        $user->name = "Budiyono";
        $user->password = password_hash("12456", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserLoginRequest();

        $request->id = "budi2";
        $request->password = "12456a";

        $this->userService->login($request);
    }

    public function testLoginSucess()
    {
       
        $user = new User();
        $user->id = "budi2";
        $user->name = "Budiyono";
        $user->password = password_hash("12456", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
        $request = new UserLoginRequest();

        $request->id = "budi2";
        $request->password = "12456";

        $response = $this->userService->login($request);
        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
}
