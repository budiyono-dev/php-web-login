<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {

    public function testRender(){
        View::render('Home/index',[
            "PHP Login Management"
        ]);
    
        $this->expectOutputRegex("[PHP Login Management]");
        $this->expectOutputRegex("[html]");
        $this->expectOutputRegex("[body]");

    }


}