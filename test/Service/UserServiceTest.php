<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;

use function PHPUnit\Framework\assertEquals;

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

        $request->id = "budi";
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
        $user->id = "budi";
        $user->name = "Budiyono";
        $user->password = "123456";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);
        $req = new UserRegisterRequest();
        $req->id = "budi";
        $req->name = "Budiyono";
        $req->password = "123456";

        $this->userService->register($req);
    }
}
