<?php
namespace app\Middleware;

use app\App\View;
use app\Config\Database;
use app\Repository\SessionRepository;
use app\Repository\UserRepository;
use app\Service\SessionService;

class MustLoginMiddleware implements Middleware {
    private SessionService $sessionService;

    public function __construct() {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void {
        $user = $this->sessionService->current();
        if($user == null){
            View::redirect('/users/login');
        }
    }
}
