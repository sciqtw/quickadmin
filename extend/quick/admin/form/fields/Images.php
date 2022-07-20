<?php
declare (strict_types = 1);

namespace quick\admin\form\fields;


use quick\admin\components\Component;
use quick\admin\components\ImageUpload;
use quick\admin\Element;
use quick\admin\form\Form;

class Images extends Field
{


    public $component = 'form-images-field';


    public $valueType = 'array';

    public $uploadProps = [];


    /**
     * @var string 上传类型 image|images|file
     *
     */
    public $uploadType = 'image';


    public function init()
    {
        $module = config('quick.auth_key','admin');
        $this->uploadUrl(app()->request->baseFile()."/{$module}/resource/attachment/upload");
        $this->props('moduleName',$module);
//        $this->uploadUrl("admin.php/admin/index/upload");
        return $this;
    }


    /**
     * 最大上传数量
     *
     * @param int $max
     * @return Upload
     */
    public function max(int $max)
    {

        return $this->uploadProps('max',$max);
    }


    /**
     *  限制文件大小
     * @param int $size 文件大小 M
     * @return Upload
     */
    public function fileSize(int $size)
    {
        return $this->uploadProps('size',$size);
    }


    /**
     * 上传地址
     *
     * @param string $url
     * @return Upload
     */
    public function uploadUrl(string $url)
    {
        return $this->props('action',$url);
    }


    /**
     * @return array|string
     */
    public function getDefaultValue()
    {
        $value = parent::getDefaultValue();

        if(is_string($value) && !empty($value)){
            $value = explode(",",$value);
            $data = [];
            foreach($value as $url){
                $data[] = $url;
            }
            $value = $data;
        }

        $value = is_array($value) ? $value:[];
        return $value;
    }


    /**
     * 上传地址
     * @param string $action 上传地址
     * @return Upload
     */
    public function action(string $action)
    {
        return $this->uploadProps('action',$action);
    }


    /**
     * 附带参数
     * @param array $data 附带参数
     * @return Upload
     */
    public function data(array $data)
    {
        return $this->uploadProps(__FUNCTION__,$data);
    }



    /**
     * 提交字段名
     * @param string $name 附带参数
     * @return Upload
     */
    public function name(string $name)
    {
        return $this->uploadProps(__FUNCTION__,$name);
    }


    /**
     * @param string $url 默认值
     * @return Upload
     */
    public function url(string $url)
    {
        return $this->uploadProps('url',$url);
    }



    /**
     * @param int $width
     * @return Upload
     */
    public function width($width)
    {
        return $this->uploadProps('width',$width);
    }


    /**
     * @param int $height
     * @return Upload
     */
    public function height(int $height)
    {
        return $this->uploadProps('height',$height);
    }


    /**
     * 显示tip提示
     * @param bool $notip
     * @return Upload
     */
    public function notip(bool $notip = true)
    {
        return $this->uploadProps('notip',$notip);
    }


    /**
     *  限制上传类型
     * @param array $ext
     * @return Upload
     */
    public function ext(array $ext = ['jpg', 'png', 'gif', 'bmp'])
    {
        return $this->uploadProps('ext',$ext);
    }


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function uploadProps($name, $value = '')
    {
        if (is_array($name)) {
            $this->withUploadProps($name);
        } else {
            $this->withUploadProps([$name => $value]);
        }
        return $this;
    }


    /**
     * @param array $props
     * @return $this
     */
    protected function withUploadProps(array $props)
    {
        $this->uploadProps = array_merge($this->uploadProps, $props);

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->valueType = "array";
        $this->props('type',$this->uploadType);
        $this->props('uploadProps',$this->uploadProps);
        return array_merge(parent::jsonSerialize(), []);
    }
}
