<?php
declare (strict_types=1);

namespace quick\admin\actions;


use quick\admin\annotation\AdminAuth;
use quick\admin\http\model\Model;
use quick\admin\http\response\JsonResponse;
use think\facade\Db;

/**
 *
 * @AdminAuth(auth=true,menu=true,login=true,title="Edit")
 * @package quick\actions
 */
class EditAction extends RowAction
{

    public $name = "编辑";



    protected function handle($model, $request)
    {

        $form = $this->form();
        $data = (array)$form->getSubmitData($this->request, 2);
        Db::startTrans();
        try {
            $data[self::$pk] = $model[self::$pk];
            if ($this->beforeSavingCallback instanceof \Closure) {
                $beforeSavingCallback = \Closure::bind($this->beforeSavingCallback,$this);
                $data = call_user_func($beforeSavingCallback, $data, $request);
            }

            /** @var Model $model */
            $res = $model->save($data);
            if (!$res) {
                throw new \Exception("编辑失败：".$model->getFirstError());
            }

            if ($this->afterSavingCallback instanceof \Closure) {
                $afterSavingCallback = \Closure::bind($this->afterSavingCallback,$this);
                $res = call_user_func($afterSavingCallback, $data, $request);
                if ($res === false) {
                    throw new \Exception("编辑失败");
                }
            }

            if ($res instanceof JsonResponse) {
                $response = $res;
            } else {
                $response = $this->response()->success("编辑成功");
            }

            if ($this->isPage()) {
                $response->push($this->backUrl("index"));
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $response = $this->response()->error($e->getMessage());
        }

        return $response;
    }


    /**
     * @param $request
     * @param $model
     * @return \quick\admin\http\response\JsonResponse
     */
    protected function resolve($request, $model)
    {
        $form = $this->form();
        $form->fixedFooter();
        $form->hideReset();
        $form->url($this->storeUrl([
            self::$keyName => $request->param(self::$keyName)
        ]));
        $form->resolve($model);
        $form->style("background-color", '#FFFFFF');
        $form = $this->resolveComponent($form);
        return $this->response()->success('success', $form);
    }


}
