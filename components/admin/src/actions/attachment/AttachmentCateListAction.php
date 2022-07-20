<?php
declare(strict_types=1);

namespace components\admin\src\actions\attachment;


use quick\admin\actions\Action;
use quick\admin\annotation\AdminAuth;

/**
 * 设置密码
 * @AdminAuth(title="设置密码",auth=true,login=true,menu=false)
 * @package app\admin\resource\example\actions
 */
class AttachmentCateListAction extends Action
{


  public function load()
  {
      return $this->response()->success('success', []);
  }


}
