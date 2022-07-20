<?php
declare (strict_types=1);

namespace quick\admin\contracts;


use think\Request;

interface AuthInterface
{


    /**
     * @param $username
     * @param $password
     * @param int $keepTime
     * @return mixed
     */
    public function login($username, $password, $keepTime = 0);


    /**
     * @return mixed
     */
    public function logout();


    /**
     * @param string $node
     * @return mixed
     */
    public function check(string $node);


    /**
     * @return mixed
     */
    public function getUserInfo();


    /**
     * @param Object $object
     * @param string $action
     * @param Request $request
     * @return mixed
     */
    public function checkAuth(Object $object, string $action, Request $request);


}
