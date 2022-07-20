<?php
declare (strict_types=1);


namespace quick\admin\components;



use quick\admin\components\element\ElDescriptions;
use quick\admin\components\element\ElDescriptionsItem;

class QkDescriptions extends ElDescriptions
{


    public $component = "qk-descriptions";


    /**
     * @param array $data
     * @param string $label
     * @param string $content
     * @param array $itemProps
     * @return $this|ElDescriptions
     */
    public function data(array $data,$label = 'label',$content = 'content',array $itemProps= [])
    {
        $list = [];
        foreach($data as $item){
            /** @var ElDescriptionsItem $descriptionsItem */
            $descriptionsItem = $this->addItem($item,$label,$content);
            $descriptionsItem->props($itemProps);
            $descriptionsItem->width(100);
            $list[]  = $descriptionsItem;
        }

        !empty($list) && $this->props('items',$list);

        return $this;
    }

}
