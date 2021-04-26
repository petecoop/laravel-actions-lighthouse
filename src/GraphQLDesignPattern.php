<?php

namespace Petecoop\LaravelActionsLighthouse;

use Lorisleiva\Actions\BacktraceFrame;
use Nuwave\Lighthouse\Schema\ResolverProvider;
use Lorisleiva\Actions\DesignPatterns\DesignPattern;

class GraphQLDesignPattern extends DesignPattern
{
    public function getTrait(): string
    {
        return AsGraphQL::class;
    }

    public function recognizeFrame(BacktraceFrame $frame): bool
    {
        return $frame->matches(ResolverProvider::class, 'provideResolver');
    }

    public function decorate($instance, BacktraceFrame $frame)
    {
        return app(GraphQLDecorator::class, ['action' => $instance]);
    }
}
