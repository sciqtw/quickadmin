<?php

namespace quick\admin\annotation;

use Doctrine\Common\Annotations\Annotation;


/**
 * Class AdminAuth
 * @Annotation
 * @Target({"METHOD","CLASS"})
 * @package quick\annotation
 */
class AdminAuth
{


    /**
     * @Required
     * @var string 权限名称
     */
    public $title;

    /**
     * @Annot
     * @var bool 节点权限
     */
    public $auth;

    /**
     * @var bool 菜单
     */
    public $menu;

    /**
     *  如果不填就以访问链接为权限节点
     * @var string 令牌
     */
    public $node;

    /**
     *
     * @var bool 登录权限
     */
    public $login;

    //@Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
    /**
     * @var array 有效请求类型
     */
    public $method;


    //@Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
    /**
     * @var array 排除请求类型
     */
    public $ignore;

    /**
     * @param string $default
     * @return string
     */
    public function getTitle(string $default = '')
    {
        return $this->title ?? $default;
    }

    /**
     * @return int
     */
    public function getMenu()
    {
        return $this->menu ? 1 : 0;
    }

    /**
     * @return int
     */
    public function getLogin()
    {
        return $this->login ? 1 : 0;
    }

    /**
     * @return int
     */
    public function getAuth()
    {
        return $this->auth ? 1 : 0;
    }

    /**
     * @param string $default
     * @return string
     */
    public function getNode(string $default = '')
    {
        return empty($this->node) ? $default : $this->node;
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        [$method, $ignore] = [[], []];


        if (!empty($this->ignore)) {
            foreach ($this->ignore as $item) {
                $ignore[] = strtoupper($item);
            }
            $data = ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS", "HEAD"];
        } else {
            $data = $this->method ?? [];
        }

        foreach ($data as $v) {
            $v = strtoupper($v);
            if (empty($ignore) || !in_array($v, $ignore)) {
                $method[] = $v;
            }
        }

        return $method;
    }

}