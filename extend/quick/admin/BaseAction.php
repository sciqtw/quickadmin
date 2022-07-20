<?php


namespace quick\admin;


use quick\admin\http\model\traits\Validate;
use think\exception\ValidateException;
use ReflectionClass;

/**
 * Class BaseAction
 * @package plugins\demo\library
 * @property array $attributes Attribute values (name => value).
 */
class BaseAction
{

    use Validate;

    /**
     *  验证数据
     *
     * @param string $scene 验证场景
     * @param bool $batch 是否批量验证
     * @return bool
     */
    public function validate(string $scene = '', bool $batch = true)
    {
        try {
            $this->_validate($scene, $batch, true)->check($this->getAttributes());
        } catch (ValidateException $e) {
            $err = $e->getError();
            $this->_errors = is_array($err) ? $err : [$err];
            return false;
        }
        return true;
    }


    /**
     * @return array
     */
    public function safeAttributes(): array
    {
        $fields = array_keys($this->rules());
        $scenario = $this->getScenario();
        $scenarios = $this->checkScene();
        if (!empty($scenario)) {
            return !isset($scenarios[$scenario]) ? [] : $scenarios[$scenario];
        }

        return $fields;
    }


    public function attributes()
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }


    /**
     * @param null $names
     * @param array $except 排除
     * @return array
     */
    public function getAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }


    /**
     * 设置属性值
     * @param array $data
     * @param bool $safeOnly
     * @return $this
     * @throws \Exception
     */
    public function setData(array $data, $safeOnly = true)
    {
        $this->setAttributes($data,$safeOnly);
        return $this;
    }


    /**
     * @param $values
     * @param bool $safeOnly
     * @throws \Exception
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
//                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }


    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function onUnsafeAttribute($name, $value)
    {
        if (app()->isDebug()) {
            throw new \Exception("Failed to set unsafe attribute '$name' in '" . get_class($this) . "'.");
        }
    }


    /**
     * @param $name
     * @return mixed
     * @throws \think\Exception
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new \think\Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new \think\Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }


    /**
     * @param $name
     * @param $value
     * @throws \think\Exception
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // set property
            $this->$setter($value);

            return;
        }

        throw new \think\Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

}
