<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\components\Component;
use quick\admin\form\Form;
use quick\admin\table\Table;

class JsonField extends Field
{


    public $component = 'form-json-field';


    protected $valueType = 'array';

    /**
     * @var
     */
    protected $max;

    /**
     * @var
     */
    protected $min;

    protected $table;

    /**
     * @var string
     */
    protected $keyLabel;

    /**
     * @var string
     */
    protected $valueLabel;

    protected $callback;

    protected $fieldType = 'table';

    /**
     * JsonField constructor.
     * @param string $column
     * @param string $title
     * @param \Closure|null $closure
     */
    public function __construct(string $column, string $title = '', ?\Closure $closure = null)
    {
        $this->column = $column;
        $this->title = $title ?: $column;
        $this->callback = $closure;
        $this->init();
    }


    /**
     * @param $value
     * @return mixed
     */
    public function transform($value)
    {
        list($lists, $data) = [[], json_decode($value, true)];
        if (empty($data)) {
            return [];
        }

        if ($this->fieldType === 'list') {
            return $data['key'];
        }

        $keys = array_keys($data);
        foreach ($data[$keys[0]] as $i => $value) {
            $item = [];
            foreach ($keys as $key) {
                $item[$key] = $data[$key][$i];
            }
            $lists[] = $item;
        }

        if ($this->fieldType === 'keyValue') {
            $data = [];
            foreach ($lists as $value) {
                $data[$value['key']] = $value['value'];
            }
            $lists = $data;
        }

        return $lists;
    }

    /**
     * @return $this
     */
    public function setKeyValue()
    {
        $this->fieldType = 'keyValue';
        $this->callback = function (Table $table) {
            $table->column('key', __($this->keyLabel ?: 'key'))->field(function ($form) {
                return $form->text('key')->required()->hiddenLabel();
            });
            $table->column('value', __($this->valueLabel ?: 'value'))->field(function ($form) {
                return $form->text('value')->required()->hiddenLabel();
            });
        };
        return $this;
    }

    /**
     * @return $this
     */
    public function setList()
    {
        $this->fieldType = 'list';
        $this->callback = function (Table $table) {
            $table->column('key', __($this->keyLabel ?: 'key'))->field(function ($form) {
                return $form->text('key')->required()->hiddenLabel();
            });
        };
        return $this;
    }

    /**
     * @param string $keyLabel
     * @return $this
     */
    public function keyLabel(string $keyLabel)
    {
        $this->keyLabel = $keyLabel;
        return $this;
    }


    /**
     * @param string $valueLabel
     * @return $this
     */
    public function valueLabel(string $valueLabel)
    {
        $this->valueLabel = $valueLabel;
        return $this;
    }


    /**
     * 最小个数
     *
     * @param int $num
     * @return $this
     */
    public function min(int $num)
    {
        $this->min = $num;
        $this->rules('min:' . $num);
        return $this;
    }


    /**
     * 最大个数
     *
     * @param int $num
     * @return $this
     */
    public function max(int $num)
    {
        $this->max = $num;
        $this->rules('max:' . $num);
        return $this;
    }


    /**
     * @return Table
     */
    public function createTable()
    {
        if (!$this->table) {
            $table = Table::make();
            $table->disableActions();
            $table->disablePagination();
            $this->table = $table;
            if ($this->callback instanceof \Closure) {
                \Closure::bind($this->callback, $this);
                call_user_func($this->callback, $table);
            }
        }

        return $this->table;
    }


    /**
     * @return Table
     */
    public function getTable()
    {
        $table = $this->createTable();
        $table->setData($this->getTableData());
        return $table;
    }


    public function getTemplateFields()
    {
        $table = $this->getTable();
        $table->setData([['key' => '', 'value' => '']]);
        return $table->buildData()['data'];
    }

    public function getTableData()
    {
        $data = [];
        if ($this->fieldType == 'keyValue') {
            $value = (array)$this->getDefaultValue();
            foreach ($value as $k => $v) {
                $data[] = [
                    'key' => $k,
                    'value' => $v,
                ];
            }
        } elseif ($this->fieldType == 'list') {
            $value = (array)$this->getDefaultValue();
            foreach ($value as $k => $v) {
                $data[] = [
                    'key' => $v,
                ];
            }
        } else {
            $data = $this->getDefaultValue();
        }
        $data = is_array($data) ? $data:[];


        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function jsonSerialize()
    {


        $this->props([
            'fieldJson' => $this->getTemplateFields(),
            'table' => $this->getTable(),
            'fieldType' => $this->fieldType,
            'min' => $this->min ?: 0,
            'max' => $this->max ?: 0,
        ]);
        return array_merge(parent::jsonSerialize(), []);
    }

}
