<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use app\common\model\SystemArea;
use Closure;
use quick\admin\Element;
use quick\admin\metable\Metable;
use think\Exception;

class Cascader extends Field
{


    public $component = 'form-cascader-field';

    /**
     * @var string
     */
    protected $valueType = 'number';

    private $cascaderType;

    /**
     * @var
     */
    public $default;


    /**
     * @var
     */
    protected $options;

    /**
     * @var string
     */
    protected $keyName;

    /**
     * @var string
     */
    protected $labelName;

    /**
     * @var
     */
    protected $disabledName;

    /**
     * option显示组件
     *
     * @var Element
     */
    protected $optionDisplay;

    /**
     * @var array
     */
    protected $props = [];

    /**
     * 设置option显示组件
     *
     * @param Element $component
     * @return $this
     */
    public function optionDisplay(Element $component)
    {
        $this->optionDisplay = $component;
        return $this;
    }

    /**
     * @return bool|Element
     */
    protected function _getOptionDisplay()
    {
        return $this->optionDisplay ?? false;
    }

    public function transform($value)
    {
        if($this->cascaderType == 'region'){
            $value = SystemArea::getValuesByIds($value);
            return $value;
        }
        return $value;
    }

    /**
     * 可清空单选
     *
     * @return $this
     */
    public function clearable()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }

    /**
     * 启用多选
     *
     * @return $this
     */
    public function multiple()
    {
        $this->valueType = 'array';
        $this->withProps([__FUNCTION__ => true]);
        return $this;
    }

    /**
     * 设置父子节点取消选中关联，从而达到选择任意一级选项的目的。
     *
     * @return $this
     */
    public function checkStrictly()
    {
        $this->withProps([__FUNCTION__ => true]);
        return $this;
    }

    /**
     * 启用多选 合并为一段文字
     *
     * @return $this
     */
    public function collapseTags()
    {
        $this->multiple();
        $this->attribute('collapse-tags', true);
        return $this;
    }

    /**
     * hover 触发子菜单
     *
     * @return $this
     */
    public function hover()
    {
        $this->withProps(['expandTrigger' => 'hover']);
        return $this;
    }

    /**
     * 选项分隔符
     *
     * @param string $value
     * @return $this
     */
    public function separator(string $value)
    {
        $this->attribute('separator', $value);
        return $this;
    }

    /**
     * 开启动态加载
     *
     * @param string $url
     * @return $this
     */
    public function lazy(string $url)
    {
        $this->withProps(['lazy' => true]);
        $this->attribute('url', $url);
        return $this;
    }

    /**
     * 开启动态加载
     *
     * @param string $url
     * @return $this
     */
    public function load(string $url)
    {
        $this->attribute('load', $url);
        return $this;
    }


    /**
     * @return $this
     */
    public function panel()
    {
        $this->attribute('panel', true);
        return $this;
    }


    /**
     * @param $options
     * @return $this
     */
    public function options($options, $callback = null)
    {
        if (is_callable($callback)) {
            $callback = Closure::bind($callback, $this);
            $options = call_user_func($callback, $options);
        }
        $this->options = $options;
        return $this;
    }


    /**
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function region()
    {
        $areaOptions = SystemArea::cascaderOptions();
        $this->options($areaOptions);
        $this->cascaderType = 'region';
        $this->valueType = 'array';
        return $this;
    }


    /**
     * 是否允许用户创建新条目，需配合 filterable 使用
     *
     * @return $this
     */
    public function filterable()
    {
        $this->attribute('filterable', true);
        return $this;
    }


    /**
     * @param array $options
     * @param string $keyName
     * @param string $labelName
     * @param string $disabled
     * @return array
     */
    public function formatOptions(array $options, string $keyName = 'value', string $labelName = 'label', string $disabled = "disabled")
    {
        $list = collect($options)->map(function ($item) use ($keyName, $labelName, $disabled) {

            if (is_callable($disabled)) {
                $disabled = call_user_func($disabled, $item);
            } else {
                $disabled = isset($item[$disabled]) ? $item[$disabled] : false;
            }
            return array_merge($item, [
                'value' => $item[$keyName],
                'label' => $item[$labelName],
                'disabled' => $disabled,
            ]);
        })->toArray();
        return $list;
    }

    /**
     * 远程搜索
     *
     * @return $this
     */
    public function remote(string $url)
    {
        $this->attribute('url', $url);
        $this->attribute('remote', true);
        $this->filterable();
        return $this;
    }


    /**
     * 隐藏显示选中值的完整路径
     *
     * @return $this
     */
    public function hideAllLevels()
    {
        $this->attribute('show-all-levels', false);
        return $this;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $props
     * @return $this
     */
    public function withProps(array $props)
    {
        $this->props = array_merge($this->props, $props);

        return $this;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->attribute('props', $this->props);
        $this->attribute('options', $this->getOptions());
        $this->attribute('optionDisplay', $this->_getOptionDisplay());
        return array_merge(parent::jsonSerialize(), []);
    }
}
