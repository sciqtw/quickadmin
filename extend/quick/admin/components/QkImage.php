<?php
declare (strict_types = 1);

namespace quick\admin\components;


use quick\admin\Element;

/**
 * 自定义组件
 *
 * Class Custom
 * @package quick\components
 */
class QkImage extends Element
{


    public $component = 'qk-image';


    protected $src;


    /**
     * ElImage constructor.
     * @param string $src
     * @param int $width
     * @param int $height
     */
    public function __construct(string $src, int $width = 60, int $height = 60)
    {
        $this->src($src);
        $this->width($width);
        $this->height($width);
    }
    /**
     * @param int $width
     * @return $this
     */
    public function height(int $width)
    {
        $this->style("height", $width . "px");
        return $this;
    }


    /**
     * @param int $width
     * @return $this
     */
    public function width(int $width)
    {
        $this->style("width", $width . "px");
        return $this;
    }


    /**
     * 确定图片如何适应容器框，同原生 object-fit
     *
     * @param string $type string    fill / contain / cover / none / scale-down
     * @return $this
     */
    public function fit(string $type)
    {
        $this->attribute("fit", $type);
        return $this;

    }


    /**
     * 是否开启懒加载
     *
     * @param bool $lazy
     * @return $this
     */
    public function lazy(bool $lazy)
    {
        $this->attribute("lazy", $lazy);
        return $this;
    }


    /**
     *  图片地址
     * @param string $url
     * @return $this
     */
    public function src(string $url)
    {
        $this->src;
        $this->attribute(__FUNCTION__, $url);
        return $this;
    }


    /**
     *  图片预览
     *
     * @param array $data
     * @return $this
     */
    public function previewSrcList(array $data)
    {
        $this->attribute('preview-src-list', $data);
        return $this;
    }


}
