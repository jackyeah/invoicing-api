<?php

namespace App\Http\RepositoryProtocol\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait RewriteTrait
{
    /**
     * 將hasMany 切連線至master
     * Define a one-to-many relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @param  string $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $instance->setConnection('master');
        $instance->getConnection()->enableQueryLog();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }
}