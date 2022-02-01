<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;

class HomeController
{

    function index(): void
    {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);
    }

}