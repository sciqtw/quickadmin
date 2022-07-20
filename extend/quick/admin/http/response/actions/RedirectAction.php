<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class RedirectAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'redirect';


    /**
     * RedirectAction constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url($url);
    }


    /**
     * 地址
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->params = array_merge($this->params, ['url' => $url]);
        return $this;
    }



}
