<?php

namespace Petecoop\LaravelActionsLighthouse;

use Closure;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;
use ReflectionFunction;

class ActionValidationDirective extends ValidationDirective
{
    protected array $rules = [];
    protected array $messages = [];

    /**
     * Gets the validation info from the action through reflection...
     * Not pretty but it works!
     */
    public function handleField(FieldValue $fieldValue, Closure $next): FieldValue
    {
        $resolver = $fieldValue->getResolver();

        $fieldValue->useDefaultResolver();
        $actionClosure = $fieldValue->getResolver();
        $fieldValue->setResolver($resolver);

        $reflect = new ReflectionFunction($actionClosure);
        $decorator = $reflect->getClosureThis();
        $action = $decorator->getAction();

        if (method_exists($action, 'rules')) {
            $this->rules = $action->rules();
        }

        if (method_exists($action, 'getValidationMessages')) {
            $this->messages = $action->getValidationMessages();
        }

        return parent::handleField($fieldValue, $next);
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
