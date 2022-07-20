<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class FooterField extends Field
{


    public $component = 'form-footer-field';



    public function __construct()
    {

        parent::__construct('_footer', '');
    }


    /**
     * 隐藏取消按钮
     *
     * @return FooterField
     */
    public function hideCancel()
    {
        return $this->props('showCancel',false);
    }


    /**
     * 隐藏取消按钮
     *
     * @return FooterField
     */
    public function showCancel()
    {
        return $this->props('showCancel',true);
    }


    /**
     * @param Element $submitBtn
     * @return FooterField
     */
    public function submitBtn(Element $submitBtn)
    {
        return $this->props('submitBtn',$submitBtn);
    }


    /**
     * @param Element $submitBtn
     * @return FooterField
     */
    public function resetBtn(Element $resetBtn)
    {
        return $this->props('resetBtn',$resetBtn);
    }


    /**
     * @param Element $cancelBtn
     * @return FooterField
     */
    public function cancelBtn(Element $cancelBtn)
    {
        return $this->props('cancelBtn',$cancelBtn);
    }


    /**
     * 隐藏重置按钮
     *
     * @return FooterField
     */
    public function hideReset()
    {
        return $this->props('showReset',false);
    }

    /**
     * 默认值
     * @param number $value
     * @return $this|Field
     */
    public function default($value)
    {
        $this->default = $value;
        return $this;
    }


    /**
     * 固定在底部
     * @return $this
     */
    public function fixedBottom()
    {
        $this->props('fixed',true);
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
