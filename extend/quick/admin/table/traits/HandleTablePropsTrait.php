<?php
declare (strict_types=1);

namespace quick\admin\table\traits;


trait HandleTablePropsTrait
{

    /**
     * 禁用table右边工具条
     *
     * @return $this
     */
    public function disableRightTools()
    {
        $this->withMeta(['disableRightTools' => true]);
        return $this;
    }


    /**
     * 显示table刷新按钮
     *
     * @return $this
     */
    public function showRefresh()
    {
        $this->props(['showRefresh' => true]);
        return $this;
    }


    /**
     * show 配置表格尺寸按钮
     *
     * @return $this
     */
    public function showSize()
    {
        $this->props(['showSize' => true]);
        return $this;
    }


    /**
     * 禁用table栏目显示配置工具
     *
     * @return $this
     */
    public function showColumnSelector()
    {
        $this->props(['showColumnSelector' => true]);
        return $this;
    }


    /**
     * 创建带斑马纹的表格
     *
     * @return $this
     */
    public function stripe()
    {
        $this->withAttributes([__FUNCTION__ => true]);
        return $this;
    }

    /**
     * height属性为 Table 指定高度
     *
     * @param int|string $height
     * @return $this
     */
    public function height($height)
    {
        $this->withAttributes([__FUNCTION__ => $height]);
        return $this;
    }

    /**
     * max-height属性为 Table 指定最大高度
     *
     * @param $height
     * @return $this
     */
    public function maxHeight($height)
    {
        $this->withAttributes(["max-height" => $height]);
        return $this;
    }


    /**
     * 设置默认尺寸
     *
     * @param string $size medium|small|mini
     * @return mixed
     */
    public function defaultSize(string $size)
    {
        return $this->props(['tableSize' => $size]);
    }

    /**
     * 显示多选
     *
     * @return mixed
     */
    public function showSelection()
    {
        return $this->props([
            'showSelection' => true,
            'selectionType' => 'checkbox',
        ]);
    }

    /**
     * 显示多选
     *
     * @return mixed
     */
    public function radio()
    {
        $this->showConfirmBtn();
        return $this->props([
            'showSelection' => true,
            'selectionType' => 'radio',
        ]);
    }

    public function showConfirmBtn()
    {
        return $this->props([
            'showConfirmBtn' => true,
        ]);
    }

    /**
     * 隐藏过滤器
     *
     * @return mixed
     */
    public function hideFilter()
    {
        return $this->withMeta(['hideFilter' => true]);
    }


}
