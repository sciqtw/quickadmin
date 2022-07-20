<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class DownloadAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'download';


    /**
     * DownloadAction constructor.
     * @param string $link 下载地址
     * @param string $name 名称
     */
    public function __construct(string $link,string $name = '')
    {
        $this->link($link);
        $name && $this->name($name);
    }


    /**
     * 地址
     * @param string $link
     * @return $this
     */
    public function link(string $link)
    {
        $this->params = array_merge($this->params, ['link' => $link]);
        return $this;
    }


    /**
     * 名称
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->params = array_merge($this->params, ['name' => $name]);
        return $this;
    }


}
