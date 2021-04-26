<?php

namespace Petecoop\LaravelActionsLighthouse;

use Lorisleiva\Actions\ActionManager;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(ActionManager::class, function ($actionManager) {
            $designPatterns = $actionManager->getDesignPatterns();
            $designPatterns[] = new GraphQLDesignPattern();
            $actionManager->setDesignPatterns($designPatterns);
        });
    }
}
