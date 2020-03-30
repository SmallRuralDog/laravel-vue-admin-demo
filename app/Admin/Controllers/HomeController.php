<?php

namespace App\Admin\Controllers;

use SmallRuralDog\Admin\Components\Alert;
use SmallRuralDog\Admin\Controllers\AdminController;
use SmallRuralDog\Admin\Layout\Content;
use SmallRuralDog\Admin\Layout\Row;

class HomeController extends AdminController
{


    public function index(Content $content)
    {
        $content->className('m-10')
            ->row(function (Row $row) {
                $row->gutter(20);
                $row->column(12, Alert::make("你好，同学！！", "欢迎使用 laravel-vue-admin")->showIcon()->closable(false)->type("success"));
                $row->column(12, Alert::make("你好，同学！！", "欢迎使用 laravel-vue-admin")->showIcon()->closable(false)->type("error"));
            })->row(function (Row $row) {
                $row->gutter(20);
                $row->className('mt-10');
                $row->column(12, Alert::make("你好，同学！！", "欢迎使用 laravel-vue-admin")->showIcon()->closable(false)->type("info"));
                $row->column(12, Alert::make("你好，同学！！", "欢迎使用 laravel-vue-admin")->showIcon()->closable(false)->type("warning"));
            });
        return $content;
    }
}

