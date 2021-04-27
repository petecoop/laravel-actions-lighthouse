<?php

namespace Petecoop\LaravelActionsLighthouse;

use ReflectionFunction;
use GraphQL\Language\Parser;
use GraphQL\Language\AST\Node;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use GraphQL\Language\AST\FieldDefinitionNode;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Values\TypeValue;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use GraphQL\Language\AST\ObjectTypeDefinitionNode;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldManipulator;
use Nuwave\Lighthouse\Support\Contracts\ArgumentSetValidation;

class ActionValidatorDirective extends BaseDirective implements ArgumentSetValidation, FieldManipulator
{
    protected $action;

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @actionValidator(class: String) on FIELD_DEFINITION
GRAPHQL;
    }

    public function rules(): array
    {
        $action = $this->action();
        if (method_exists($action, 'rules')) {
            return $action->rules();
        }

        return [];
    }

    public function messages(): array
    {
        $action = $this->action();
        if (method_exists($action, 'getValidationMessages')) {
            return $action->getValidationMessages();
        }

        return [];
    }

    public function attributes(): array
    {
        $action = $this->action();
        if (method_exists($action, 'getValidationAttributes')) {
            return $action->getValidationAttributes();
        }

        return [];
    }

    protected function action()
    {
        if ($this->action === null) {
            $this->action = app($this->directiveArgValue('class'));
        }

        return $this->action;
    }

    /**
     * Give the validator the action class to use later
     */
    public function manipulateFieldDefinition(
        DocumentAST &$documentAST,
        FieldDefinitionNode &$fieldDefinition,
        ObjectTypeDefinitionNode &$parentType
    ) {
        $type = new TypeValue($parentType);
        $fieldValue = new FieldValue($type, $fieldDefinition);
        $fieldValue->useDefaultResolver();
        $actionClosure = $fieldValue->getResolver();

        $reflect = new ReflectionFunction($actionClosure);
        $decorator = $reflect->getClosureThis();
        $action = $decorator->getAction();

        $this->setFullClassnameOnDirective($fieldDefinition, get_class($action));
    }

    /**
     * Set the full classname of the validator class on the directive.
     *
     * This allows accessing it straight away when resolving the query.
     *
     * @param  (\GraphQL\Language\AST\TypeDefinitionNode&\GraphQL\Language\AST\Node)|\GraphQL\Language\AST\FieldDefinitionNode  $definition
     */
    protected function setFullClassnameOnDirective(Node &$definition, string $class): void
    {
        // @phpstan-ignore-next-line The passed in Node types all have the property $directives
        foreach ($definition->directives as $directive) {
            if ($directive->name->value === $this->name()) {
                $directive->arguments = ASTHelper::mergeUniqueNodeList(
                    $directive->arguments,
                    [Parser::argument('class: "'.addslashes($class).'"')],
                    true
                );
            }
        }
    }

}
