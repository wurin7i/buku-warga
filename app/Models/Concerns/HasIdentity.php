<?php

namespace App\Models\Concerns;

use App\Models\Identifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasIdentity
{
    /**
     * Store the relations
     *
     * @var array
     */
    private static $identifierRelations = [];

    public function initializeHasIdentity(): void
    {
        $identityEnum = config('identity.enum');
        collect($identityEnum::cases())->each(function ($type) {
            self::addIdentifierRelation(
                $type->value,
                fn (): HasOne => $this->identities()
                    ->one()
                    ->ofMany([
                        'id' => 'MAX'
                    ], fn (Builder $q) => $q->where('identifier_type', $type->value))
            );
        });
    }

    public function identities(): HasMany
    {
        return $this->hasMany(Identifier::class, 'person_id');
    }

    /**
     * Add a new relation
     *
     * @param $name
     * @param $closure
     */
    public static function addIdentifierRelation($name, $closure)
    {
        static::$identifierRelations[$name] = $closure;
    }

    /**
     * Determine if a relation exists in dynamic relationships list
     *
     * @param $name
     *
     * @return bool
     */
    public static function hasIdentifierRelation($name)
    {
        return array_key_exists($name, static::$identifierRelations);
    }

    /**
     * If the key exists in relations then
     * return call to relation or else
     * return the call to the parent
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (static::hasIdentifierRelation($name)) {
            // check the cache first
            if ($this->relationLoaded($name)) {
                return $this->relations[$name];
            }

            // load the relationship
            return $this->getRelationshipFromMethod($name);
        }

        return parent::__get($name);
    }

    /**
     * If the method exists in relations then
     * return the relation or else
     * return the call to the parent
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (static::hasIdentifierRelation($name)) {
            return call_user_func(static::$identifierRelations[$name], $this);
        }

        return parent::__call($name, $arguments);
    }
}
