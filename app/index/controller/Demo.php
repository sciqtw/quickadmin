<?php
declare(strict_types=1);

namespace app\index\controller;



class Demo
{


    /**
     * @return \think\response\View
     */
    public function index()
    {

        return view('index', []);
    }

    public function articles()
    {
        return view('articles', []);
    }



    public function detail()
    {
        return view('detail', []);
    }

    public function login()
    {
        return view('login', []);
    }

    public function register()
    {
        return view('register', []);
    }
}
