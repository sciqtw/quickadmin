<?php

namespace quick\admin\components;

use quick\admin\components\element\ElButton;
use quick\admin\components\element\ElCard;
use quick\admin\components\element\ElDescriptions;
use quick\admin\components\element\ElDrawer;
use quick\admin\components\element\ElImage;
use quick\admin\components\element\ElLink;
use quick\admin\components\element\ElPopover;
use quick\admin\components\element\ElTabsPane;
use quick\admin\components\element\ElTooltip;
use quick\admin\components\layout\Content;
use quick\admin\components\element\QuickIcon;
use quick\admin\components\element\Confirm;
use quick\admin\components\element\ElInput;
use quick\admin\components\element\ElTable;
use quick\admin\components\element\QuickDialog;
use quick\admin\components\layout\Row;
use quick\admin\Element;
use quick\admin\http\response\actions\Actions;
use think\Exception;

/**
 * Class Component
 * @package quick\components
 * @method static Custom           custom(string $componentName) 自定义组件
 * @method static ElInput          input(string $name, string $title)
 * @method static ElTable          table(string $name, string $title)
 * @method static QuickDialog      dialog(string $title)
 * @method static Confirm          confirm($msg, $confirm, $cancel, $title)
 * @method static QuickIcon        icon(string $icon, string $size = '',string $color = '')
 * @method static ElPopover        popover(string $content)
 * @method static ElTooltip        tooltip()
 * @method static ElButton         button(string $content = '', string $type = '', string $size = '')
 * @method static ElLink           link(string $type)
 * @method static ElImage          image(string $src,int $width,int $height)
 * @method static Content          content()
 * @method static ShowHtml         html(string $html)
 * @method static Iframe           iframe(string $src)
 * @method static ElDrawer         drawer(string $title)
 * @method static QuickTabs        tabs(array $panes = [], string $type = '')
 * @method static ElTabsPane       tabsPane(string $name, $label, $content)
 * @method static Row              row(string $type)
 * @method static QuickAction      action($name, string $type = '', string $size = '')
 * @method static ElCard           card($content = null)
 * @method static QuickPopover     quickPopover($content = null)
 * @method static InlineEdit       inline($content = null)
 * @method static QuickTree        tree(array $data = [],string $title = '')
 * @method static QkImage          qkImage(string $src,int $width,int $height)
 * @method static ElDescriptions   elDescriptions($title = '',$extra = null)
 * @method static QkDescriptions   qkDescriptions($title = '',$extra = null)
 *
 */
class Component
{

    /**
     * @var array
     */
    public static $availableComponents = [
        'custom' => Custom::class,
        'input' => ElInput::class,
        'table' => ElTable::class,
        'dialog' => QuickDialog::class,
        'confirm' => Confirm::class,
        'icon' => QuickIcon::class,
        'popover' => ElPopover::class,
        'quickPopover' => QuickPopover::class,
        'tooltip' => ElTooltip::class,
        'button' => ElButton::class,
        'link' => ElLink::class,
        'content' => Content::class,
        'html' => ShowHtml::class,
        'iframe' => Iframe::class,
        'drawer' => ElDrawer::class,
        'tabs' => QuickTabs::class,
        'tabsPane' => ElTabsPane::class,
        'row' => Row::class,
        'action' => QuickAction::class,
        'card' => ElCard::class,
        'image' => ElImage::class,
        'inline' => InlineEdit::class,
        'tree' => QuickTree::class,
        'qkImage' => QkImage::class,
        'elDescriptions' => ElDescriptions::class,
        'qkDescriptions' => QkDescriptions::class,
    ];



    public static function iconCard($icon, $name, $color)
    {
        return self::card(self::custom("a")->content([
            self::icon($icon, '30')->color($color),
            self::custom("p")->content($name)
        ]))->style(["text-align" => "center", 'cursor' => 'pointer', 'margin-bottom' => "20px"])
            ->props('shadow', "hover");
    }


    /**
     * 扩展组件
     *
     * @param string $name
     * @param $component
     */
    public static function extend(string $name, $component)
    {
        static::$availableComponents[$name] = $component;
    }

    /**
     * @param $name
     * @return bool|mixed|string
     */
    public static function findComponentClass($name)
    {

        $class = static::$availableComponents[$name] ?? '';
        if (!empty($class) && class_exists($class)) {
            return $class;
        }
        return false;
    }

    /**
     * @param $name
     * @param mixed ...$arguments
     * @return Element
     * @throws Exception
     */
    public static function create($name, ...$arguments)
    {

        if ($className = static::findComponentClass($name)) {
            /** @var Element $object */
            $object = new $className(...$arguments);
            return $object;
        }
        throw new Exception('找不到组件');
    }

    /**
     * @param $method
     * @param $arguments
     * @return Element
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        return $this->create($method, ...$arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return Element
     * @throws Exception
     */
    public static function __callStatic($method, $arguments)
    {
        return static::create($method, ...$arguments);
    }

}
