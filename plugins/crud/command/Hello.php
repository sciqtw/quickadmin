<?php
declare (strict_types = 1);

namespace plugins\demo\command;

use plugins\demo\model\GoodsWarehouseService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Hello extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('hello')
            ->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));


//        $s = new GoodsWarehouseService();
//        $s->attributes = [
//            'name' => 33
//        ];
//        $res = $s->execute();
//        dump($res);
//
//        die;

        $service  = new ServiceGenerator();
        $service->output = $output;
        $service->modelName = "MallGoodsWarehouse";
        $service->name = "GoodsWarehouseService";
        $service->create();

//        $service  = new CreateModel();
//        $service->output = $output;
////        $service->name = "MallGoodsWarehouse";
//        $service->name = "MallPaymentOrder";
//        $service->create();
        /**
         * 1.获取表信息
         * 2. 创建模型
         * 3. 创建resource
         *
         */
        // 指令输出
        $output->writeln('hello'.$name);
    }
}
