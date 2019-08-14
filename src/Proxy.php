<?php

namespace Imanghafoori\Middlewarize;

use Illuminate\Pipeline\Pipeline;

class Proxy
{
    private $callable;

    private $middlewares;

    /**
     * Proxy constructor.
     *
     * @param $callable
     * @param $middlewares
     */
    public function __construct($callable, $middlewares)
    {
        $this->callable = $callable;
        $this->middlewares = $middlewares;
    }

    public function __call($method, $params)
    {
        $pipeline = new Pipeline(app());
        return $pipeline
            ->via('handle')
            ->send($params)
            ->through($this->middlewares)
            ->then((function ($params) use ($method) {
               return ($this->$method(...$params));
            })->bindTo($this->callable, $this->callable));
    }
}
