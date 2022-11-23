<?php

namespace app\Controller;

use app\App\View;
use app\Config\Database;
use app\Repository\SessionRepository;
use app\Repository\UserRepository;
use app\Service\SessionService;

class HomeController {
    private SessionService $sessionService;

    public function __construct() {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index(): void {
        $user = $this->sessionService->current();
        // error_log("user : ".$user->id);
        if ($user == null) {
            View::render('Home/index', [
                "title" => "PHP Login Management"
            ]);
        } else {
            View::render('Home/dashboard', [
                "title" => "Dashboard",
                "user" => [
                    "name" => $user->name
                ]

            ]);
        }
    }
}
