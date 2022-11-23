<?php

namespace test\App;
use PHPUnit\Framework\TestCase;
use app\App\View;

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