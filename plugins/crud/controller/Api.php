<?php

declare(strict_types=1);

namespace plugins\crud\controller;


use app\common\controller\Backend;
use plugins\demo\library\doc\ApiConfig;
use plugins\demo\library\doc\Extractor;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\NodeService;
use think\helper\Str;

/**
 * Class Index
 * @AdminAuth(
 *     title="crud",
 *     auth=true,
 *     menu=true,
 *     login=false
 * )
 * @package app\admin\controller
 */
class Api extends Backend
{

    /**
     * @return \think\response\Json
     * @throws \ReflectionException
     */
    public function classList()
    {

        $namespace = $this->request->param('namespace/s','');
        if(empty($namespace) || (strpos($namespace, "app") !== 0 && strpos($namespace, "plugins") !== 0)){
            return json([
                'code' => 0,
                'msg' => '',
                'data' => [],
            ]);
        }

        $demo = root_path($namespace);
        $files = NodeService::instance()->scanDirectory($demo);

        $ignore = ["__construct", 'success', 'error', 'form', 'table'];
        foreach ($files as $file) {

            if (preg_match("|/(\w+)/(\w+)/controller/(.+)\.php$|i", $file, $matches)) {

                list(, $namespace, $application, $baseclass) = $matches;
                $classStr = strtr("{$namespace}/{$application}/controller/{$baseclass}", '/', '\\');

                if (!is_subclass_of($classStr, \app\common\controller\Api::class)) {
                    continue;
                }
                $class = new \ReflectionClass($classStr);
                $baseclass = $this->snakeNode($baseclass);
                $prefix = strtolower(strtr("{$application}/" . $baseclass, '\\', '/'));
                $application = strtolower($application);

                $plugin_name = $application;
                //模块节点 模块名称 $application   控制器名称 $prefix
                $classAnnotation = Extractor::getClassAnnotations($classStr);
                $colNode = [
                    'url' => $prefix,
                    'method' => '分组',
                    'level' => 0,
                    'name' => !empty($classAnnotation['ApiTitle'][0]) ? $classAnnotation['ApiTitle'][0] : $prefix,
                ];
                $children = [];
                foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (in_array($method->getName(), $ignore)) {
                        continue;
                    }
                    $methodAnnotation = Extractor::getMethodAnnotations($classStr, $method->getName());
//                    echo $classStr;
//                    halt($methodAnnotation);

                    $url_key = strtolower("{$prefix}/{$method->getName()}");
                    $apiReturn = !empty($methodAnnotation['ApiReturn']) ? $methodAnnotation['ApiReturn'][0] : '';

                    $children[] = [
                        'url' => $url_key,
                        'class' => $classStr,
                        'methodName' => $method->getName(),
                        'level' => 1,
                        'name' => !empty($methodAnnotation['ApiTitle'][0]) ? $methodAnnotation['ApiTitle'][0] : $method->getName(),
                        'title' => !empty($methodAnnotation['ApiTitle'][0]) ? $methodAnnotation['ApiTitle'][0] : $method->getName(),
                        'summary' => !empty($methodAnnotation['ApiSummary'][0]) ? $methodAnnotation['ApiSummary'][0] : '',
                        'sector' => !empty($methodAnnotation['ApiSector'][0]) ? $methodAnnotation['ApiSector'][0] : '',
                        'route' => !empty($methodAnnotation['ApiRoute'][0]) ? $methodAnnotation['ApiRoute'][0] : $url_key,
                        'method' => !empty($methodAnnotation['ApiMethod'][0]) ? $methodAnnotation['ApiMethod'][0] : 'GET',
                        'headers' => !empty($methodAnnotation['ApiHeaders']) ? $methodAnnotation['ApiHeaders'] : [],
                        'params' => !empty($methodAnnotation['ApiParams']) ? $methodAnnotation['ApiParams'] : [],
                        'returnParams' => !empty($methodAnnotation['ApiReturnParams']) ? $methodAnnotation['ApiReturnParams'] : [],
                        'apiReturn' => $apiReturn,
                    ];

                }
                $colNode['children'] = $children;
                $data[] = $colNode;
            }
        }

//        dump($data);
//        halt($files);

        return json([
            'code' => 0,
            'msg' => '',
            'data' => $data,
        ]);
    }


    public function update()
    {

        $formData = $this->request->post('formData');

        if (empty($formData) || empty($formData['class']) || empty($formData['methodName'])) {
            return json([
                'code' => 1,
                'data' => [],
                'msg' => '修改失败',
            ]);
        }
        $apiConfig = new ApiConfig();
//        $apiConfig->setApiInternal('dfd');
        $apiConfig->setApiTitle($formData['title']);
        !empty($formData['method']) && $apiConfig->setApiMethod($formData['method']);
        !empty($formData['summary']) && $apiConfig->setApiSummary($formData['summary']);
        !empty($formData['sector']) && $apiConfig->setApiSector($formData['sector']);
        !empty($formData['route']) && $apiConfig->setApiRoute($formData['route']);
        !empty($formData['apiReturn']) && $apiConfig->setApiReturn($formData['apiReturn']);
        if (is_array($formData['headers'])) {
            foreach ($formData['headers'] as $header) {
                $sample = $header['sample'] ?? '';
                $required = $header['required'] ?? false;
                $apiConfig->setApiHeaders($header['name'], $header['description'] ?? '', $header['type'], $sample, $required);
            }
        }
        if (is_array($formData['params'])) {
            foreach ($formData['params'] as $param) {
                $sample = $param['sample'] ?? '';
                $required = $param['required'] ?? false;
                $apiConfig->setApiParams($param['name'], $param['description'] ?? '', $param['type'], $sample, $required);
            }
        }

        if (is_array($formData['returnParams'])) {
            foreach ($formData['returnParams'] as $val) {
                $sample = $val['sample'] ?? '';
                $required = $val['required'] ?? false;
                $apiConfig->setApiReturnParams($val['name'], $val['description'] ?? '', $val['type'], $sample, $required);
            }
        }
        $docComment =  $apiConfig->build();
        $this->updateMethod($formData['class'],$formData['methodName'],$docComment);
        return json([
            'code' => 0,
            'data' => [],
            'msg' => '修改成功',
        ]);
    }




    /**
     * @ApiTitle  (测试名称)
     * @ApiSummary  (测试描述信息2)
     * @ApiSector  (测试分组)
     * @ApiMethod  (POST)
     * @ApiHeaders  (name=token,description=请求的Token,type=string,required=true)
     * @ApiParams  (name=id,description=会员ID,type=integer,required=true)
     * @ApiParams  (name=name,description=用户名,type=string,required=true)
     * @ApiParams  (name=data,description=扩展数据,type=object,required=true,sample={'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}})
     * @ApiReturnParams  (name=code,description=,type=integer,required=true)
     * @ApiReturnParams  (name=msg,description=,type=string,required=true,sample=返回成功)
     * @ApiReturnParams  (name=data,description=扩展数据返回,type=float,required=false,sample={'user_id':'int','user_name':'string','profile1':{'email':'string','age':'integer'}})
     * @ApiReturn  ("{ 'code':'1',  'mesg':'\u8fd4\u56de\u6210\u529f'  }")
     */
    protected function updateMethod($class, $method, $docComment)
    {
        $reflection = new \ReflectionClass($class);
        $filename = $reflection->getFileName();
        $method = $reflection->getMethod($method);

        $methodDoc = $method->getDocComment();
//        dump($methodDoc);
        $methodStr = $this->getLineContent($filename, $method->getStartLine(), $method->getEndLine());
        $searchStr = $methodDoc . "\r\n" . implode('', $methodStr);
//        echo $searchStr;
        $replaceStr = $docComment . "\r\n" . implode('', $methodStr);
//        echo $replaceStr;
        $contents = file_get_contents($filename);
        $contents = str_replace($searchStr, $replaceStr, $contents);
        file_put_contents($filename, $contents);
        echo $contents;
    }


    /**
     * 获取某个文件中从第几行到第几行得数据
     * @param $file
     * @param  $start
     * @param $end
     * @param int $length
     * @return array|bool
     */
    protected function getLineContent($file, $start, $end, $length = 40960)
    {
        $returnTxt = null; // 初始化返回
        $i = 1; // 行数

        if (!is_file($file)) {
            $this->errCode = -1;
            $this->errMessage = $file . '日志文件不存在';
            return false;
        }
        $handle = fopen($file, "r");
        $data = [];
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($i >= $start && $i <= $end) {
                $data[] = ($buffer);
            }

            if ($i > $end) {
                break;
            }

            $i++;
        }
        fclose($handle);
        if (empty($data)) {
            $this->errMessage = '没有这么多的日志数据，只有' . $this->getCount($file) . '条数据';
            $this->errCode = -20;
            return false;
        }
        return $data;
    }


    protected function handleParams($data, $name)
    {
        if (!empty($data[$name])) {
            return [];
        }

        return [];
    }

    /**
     * @param string $nodeStr
     * @return string
     */
    protected function snakeNode(string $nodeStr)
    {
        $arr = explode("/", $nodeStr);
        foreach ($arr as $k => $item) {
            $arr[$k] = Str::snake($item);
        }
        return implode("/", $arr);
    }

}
