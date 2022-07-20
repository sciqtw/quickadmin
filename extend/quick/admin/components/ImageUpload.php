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
class ImageUpload extends Element
{


    public $component = 'image-upload';


    public function __construct(string $url = '')
    {
        $this->url($url);
    }


    /**
     * 上传地址
     * @param string $action 上传地址
     * @return ImageUpload
     */
    public function action(string $action)
    {
        return $this->props('action',$action);
    }


    /**
     * 附带参数
     * @param array $data 附带参数
     * @return ImageUpload
     */
    public function data(array $data)
    {
        return $this->props(__FUNCTION__,$data);
    }



    /**
     * 提交字段名
     * @param string $name 附带参数
     * @return ImageUpload
     */
    public function name(string $name)
    {
        return $this->props(__FUNCTION__,$name);
    }


    /**
     * @param string $url 默认值
     * @return ImageUpload
     */
    public function url(string $url)
    {
        return $this->props('url',$url);
    }

    /**
     * 大小限制
     * @param int $size 上传大小
     * @return ImageUpload
     */
    public function size(int $size)
    {
        return $this->props('url',$size);
    }


    /**
     * @param int $width
     * @return ImageUpload
     */
    public function width(int $width)
    {
        return $this->props('width',$width);
    }


    /**
     * @param int $height
     * @return ImageUpload
     */
    public function height(int $height)
    {
        return $this->props('height',$height);
    }


    /**
     * 显示tip提示
     * @param bool $notip
     * @return ImageUpload
     */
    public function notip(bool $notip = true)
    {
        return $this->props('notip',$notip);
    }


    /**
     *  限制上传类型
     * @param array $ext
     * @return ImageUpload
     */
    public function ext(array $ext = ['jpg', 'png', 'gif', 'bmp'])
    {
        return $this->props('ext',$ext);
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(),[]);
    }
}
