<?php

declare(strict_types=1);

namespace Rebing\GraphQL\Tests\Database\SelectFields\NestedRelationLoadingTests;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Tests\Support\Models\User;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
    ];

    public function args()
    {
        return [
            'select' => [
                'type' => Type::boolean(),
            ],
            'with' => [
                'type' => Type::boolean(),
            ],
        ];
    }

    public function type()
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User'))));
    }

    public function resolve($root, $args, SelectFields $selectFields)
    {
        $users = User::query();

        if (isset($args['select']) && $args['select']) {
            $users->select($selectFields->getSelect());
        }

        if (isset($args['with']) && $args['with']) {
            $users->with(array_keys($selectFields->getRelations()));
        }

        return $users->orderBy('users.id')->get();
    }
}
