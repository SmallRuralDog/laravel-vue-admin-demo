<?php

namespace App\Admin\Controllers;

use SmallRuralDog\Admin\Components\Text;
use SmallRuralDog\Admin\Controllers\AdminController;
use SmallRuralDog\Admin\Layout\Content;

class HomeController extends AdminController
{


    public function index(Content $content)
    {
        $content->body(Text::make("欢迎使用！！！"));
        return $content;
    }
}

