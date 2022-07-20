<?php
declare (strict_types=1);

namespace quick\admin\table\action;


use quick\admin\annotation\AdminAuth;
use quick\admin\components\metable\HasWidthProps;

/**
 * 状态设置
 * @AdminAuth(auth=true,menu=true,login=true,title="状态设置")
 * @package quick\admin\table\action
 */
class SwitchColumn extends ActionColumn
{
    use HasWidthProps;

    /**
     * The column's component.
     *
     * @var string
     */
    public $component = 'index-switch-field';

    public $form;



    /**
     * @param $method
     * @param string $rule
     * @return $this
     */
    public function init($rule = '')
    {
        $this->activeValue(1)->inactiveValue(0);

        if(is_callable($rule)){
            $this->displayUsing($rule);
        }

        return $this;
    }

    public function load()
    {
        // TODO: Implement show() method.
    }

    public function store()
    {
        return $this->handle();
    }




    /**
     * @return mixed|string
     */
    public function loadUrl()
    {
        return $this->_actionUrl("load");
    }

    /**
     * @return mixed|string
     */
    public function storeUrl()
    {
        return $this->_actionUrl("store");
    }

    private function _actionUrl(string $func)
    {
        $module = str_replace('.', '/', app('http')->getName());
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();
        return "{$module}/resource/{$resource}/{$uriKey}/{$func}";
    }

    /**
     *
     * @param string $color
     * @return $this
     */
    public function activeColor(string $color)
    {
        $this->attribute("active-color", $color);
        return $this;
    }


    /**
     *
     * @param string $color
     * @return $this
     */
    public function inactiveColor(string $color)
    {
        $this->attribute("inactive-color", $color);
        return $this;
    }


    /**
     * @param string $text
     * @return $this
     */
    public function activeText(string $text)
    {
        $this->attribute("active-text", $text);
        return $this;
    }


    /**
     * @param string $text
     * @return $this
     */
    public function inactiveText(string $text)
    {
        $this->attribute("inactive-text", $text);
        return $this;
    }


    /**
     * @param $value
     * @return $this
     */
    public function activeValue($value)
    {
        $this->attribute("active-value", $value);
        return $this;
    }


    /**
     * @param $value
     * @return $this
     */
    public function inactiveValue($value)
    {
        $this->attribute("inactive-value", $value);
        return $this;
    }


    /**
     * @param bool $disabled
     * @return $this
     */
    public function disabled(bool $disabled = true)
    {
        $this->attribute("disabled", $disabled);
        return $this;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {

        $this->props( [
            'switch' => $this->attributes,
            'keyValue' => $this->getPkValue(),
            'keyName' => static::$keyValue,
            'colName' => static::$colValue,
            'submitUrl' => $this->storeUrl(),
            'fieldsUrl' => $this->loadUrl()
        ]);
        return array_merge(parent::jsonSerialize());

    }
}
