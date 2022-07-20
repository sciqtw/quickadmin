<?php
declare (strict_types=1);

namespace quick\admin\table\column;


use quick\admin\components\Component;

class ImageColumn extends AbstractColumn
{

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'index-image-field';
    /**
     * @var
     */
    protected $form;

    public function display($width = 60, $height = 0, $max = 3)
    {
        $value = $this->value ? $this->value : [];
        if (!is_array($value)) {
            $value = [$value];
        }
        $height = $height ? $height : $width;
        $this->props([
            "images" => $value,
            'width' => $width . 'px',
            'height' => $height . 'px',
            'max' => $max,
            "imageProps" => [
                'preview-teleported' => true
            ],
        ]);
//        return Component::image($value,$width,$height)->props('preview-teleported',true)->previewSrcList([$value]);
        return parent::display();
    }


}
