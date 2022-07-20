<?php
declare(strict_types=1);

namespace app\index\controller;



class User
{


    /**
     * @return \think\response\View
     */
    public function index()
    {

        return view('index', []);
    }

    public function album()
    {
        return view('album', []);
    }


}