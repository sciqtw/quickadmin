<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class EventAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'event';


    /**
     * EmitAction constructor.
     * @param string $event
     * @param array $data
     */
    public function __construct(string $event,array $data = [] )
    {
        $this->event($event);
        $this->data($data);
    }


    /**
     * @param string $event
     * @return $this
     */
    public function event(string $event)
    {
        return $this->withParams(['event' => $event]);
    }

    /**
     *
     * @param string $event
     * @return $this
     */
    public function isQuick()
    {
        return $this->withParams(['isQuick' => true]);
    }


    /**
     * @param array $data
     * @return eventAction
     */
    public function data(array $data)
    {
        return $this->withParams(['data' => $data]);
    }


}
