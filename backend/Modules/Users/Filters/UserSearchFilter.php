<?php

namespace Modules\Users\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class UserSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->where(function (Builder $q) use ($value) {
            $q->where('name', 'LIKE', "%{$value}%")
              ->orWhere('email', 'LIKE', "%{$value}%");
        });
    }
}
