<?php
declare (strict_types = 1);

namespace quick\admin\table;


use quick\admin\Element;

class Paginate extends Element
{


    public $component = "el-pagination";


    /**
     * @var int
     */
    public $currentPage = 1;

    /**
     * @var int
     */
    public $total = 0;


    /**
     * @var int
     */
    public $perPage = 15;


    /**
     * @var array
     */
    public $data = [];

    /**
     * Paginate constructor.
     * @param int $total
     * @param int $currentPage
     */
    public function __construct(int $total, int $currentPage = 1)
    {

        $this->layout("total, sizes, prev, pager, next, jumper")
            ->total($total)
            ->currentPage($currentPage);
    }

    /**
     * @param String $layout
     * @return Paginate
     */
    public function layout(String $layout)
    {
        return $this->props([__FUNCTION__ => $layout]);
    }

    /**
     * @param String $layout
     * @return Paginate
     */
    public function currentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @param Number $total
     * @return Paginate
     */
    public function total(int $total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param array $sizes
     * @return Paginate
     */
    public function pageSizes(array $sizes)
    {

        return $this->props(["page-sizes" => $sizes]);
    }


    /**
     * 每页显示条目个数
     *
     * @param int $size
     * @return Paginate
     */
    public function pageSize(int $size)
    {
        $this->perPage = $size;
        return $this;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * @param array $data
     * @return array
     */
    public function buildData($data = [])
    {
        if(empty($data)){
            $data = $this->data;
        }
        return [
            'per_page' => $this->perPage,
            'total' => $this->total,
            'current_page' => $this->currentPage,
            'data' => $data,
        ];
    }


    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(),[

        ]);
    }

}
