<?php

namespace test\Repository;

use PHPUnit\Framework\TestCase;
use app\Config\Database;
use app\Domain\User;
use app\Repository\UserRepository;

class UserRepositoryTest extends TestCase {
    private UserRepository $userRepository;

    protected function setUp(): void {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveUser() {
        $user = new User();
        $user->id = "budi";
        $user->name = "budiyono";
        $user->password = "123456";
        $this->userRepository->save($user);
        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function findbyIdNotFound()
    {
        # code...
        $user = $this->userRepository->findById("not found");
        self::assertNull($user);
    }
}
