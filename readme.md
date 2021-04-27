# Laravel Actions Lighthouse

Create Actions with `lorisleiva/laravel-actions` that are resolved by `nuwave/lighthouse` allowing a GraphQL query/mutation to hit an action directly.

## Install

```
composer require petecoop/laravel-actions-lighthouse
```

## GraphQL Actions

Add the `AsGraphQL` trait to your action:

```php
use Petecoop\LaravelActionsLighthouse\AsGraphQL;

class SomeAction
{
    use AsAction, AsGraphQL;
}
```

This is resolved based on name of the query in your schema.graphql:

```gql
type Query {
    someAction: SomeResult
}
```

Ensure you register the path to the handler in `config/lighthouse.php` this may need to be published first: `php artisan vendor:publish --tag=lighthouse-config`

If adding a mutation then add to mutations - this needs to be done for each folder of actions
```php
[
    'namespaces' => [
        'queries' => [
            'App\\GraphQL\\Queries',
            'App\\Actions',
            'App\\Actions\\User',
        ],
    ]
]
```

You can then use the args from GraphQL directly in your handler:

```php
class SomeAction
{
    use AsAction, AsGraphQL;

    public function handle(array $args)
    {

    }
}
```

Or use `asGraphQL` to pull out args from the graphql query:

```php
class SomeAction
{
    use AsAction, AsGraphQL;

    public function handle(int $userId)
    {
        //...
    }

    public function asGraphQL($_, $args)
    {
        return $this->handle($args['user_id']);
    }
}
```

## Validation

You can use [Laravel Action Validation Rules](https://laravelactions.com/2.x/add-validation-to-controllers.html#adding-validation-rules) by using the `@actionValidator` directive.

for example:

```gql
type Mutation {
    updateUserName(id: ID!, name: String!): User! @actionValidator
}
```

`rules()`, `getValidationMessages()` and `getValidationAttributes()` currently work.

```php
class UpdateUserName
{
    use AsAction, AsGraphQL;

    public function handle(array $args)
    {
        //...
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3'],
        ];
    }
}
```