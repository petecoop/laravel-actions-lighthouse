<?php

namespace Petecoop\LaravelActionsLighthouse;

use Illuminate\Contracts\Container\Container;
use Lorisleiva\Actions\Concerns\DecorateActions;

class GraphQLDecorator
{
    use DecorateActions;

    public function __construct($action, Container $container)
    {
        $this->setAction($action);
        $this->setContainer($container);
    }

    public function __invoke($_, $args)
    {
        if ($this->hasMethod('asGraphQL')) {
            return $this->fromActionMethod('asGraphQL', [$_, $args]);
        }

        if ($this->hasMethod('handle')) {
            return $this->fromActionMethod('handle', [$args]);
        }
    }

    public function getAction()
    {
        return $this->action;
    }
}
