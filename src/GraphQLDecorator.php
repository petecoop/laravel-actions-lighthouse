<?php

namespace Petecoop\LaravelActionsLighthouse;

use Lorisleiva\Actions\Concerns\DecorateActions;

class GraphQLDecorator
{
    use DecorateActions;

    public function __construct($action)
    {
        $this->setAction($action);
    }

    public function __invoke($_, $args)
    {
        if ($this->hasMethod("asGraphQL")) {
            return $this->fromActionMethod("asGraphQL", [$_, $args]);
        }

        return $this->action->handle(...$args);
    }

    public function getAction()
    {
        return $this->action;
    }
}
