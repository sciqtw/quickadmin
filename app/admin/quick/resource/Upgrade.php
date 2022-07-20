<?php
declare(strict_types=1);

namespace app\admin\quick\resource;


use app\admin\quick\actions\UpgradeQueueAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\components\QkTimeline;
use quick\admin\library\service\ModuleService;

/**
 * Class Index
 * @AdminAuth(title="升级管理",auth=true,menu=true,login=true )
 * @package app\admin\controller
 */
class Upgrade extends Resource
{


    protected function display()
    {
        $content = Component::content();
        $timeLine = QkTimeline::make();
        $modules = ModuleService::instance()->online();
        $locals = ModuleService::instance()->getModules();
        $current = $locals['admin']['version'] ?? 'd';
        if (isset($modules['admin'])) {
            $updateLog = $modules['admin'];
            $pattern = "|^(\d{4})\.(\d{2})\.(\d{2})\.(\d+)$|";
            $updateLog['change'] = array_reverse($updateLog['change']);
            foreach ($updateLog['change'] as $version => &$change) {

                $timeLine->add(Component::html($change),preg_replace($pattern, '$1年$2月$3日 第 $4 次更新', $version))
                    ->placement('top');
            }

        }

        $content->title('系统更新')
            ->description('当前版本：'.$current)
            ->children(UpgradeQueueAction::make(),'tools')
            ->body($timeLine);

        return $content;
    }

}
