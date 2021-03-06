<?php

namespace SingleTableInheritance;

use Illuminate\Database\Eloquent\Model;

trait SingleTableInheritance
{
    /**
     * @return bool
     */
    protected static function isImmediateChildOfEloquent()
    {
        $baseClasses = array_merge(
            [Model::class],
            static::getBaseClasses()
        );

        // So we do not have to require illuminate/foundation.
        if (class_exists('\\Illuminate\\Foundation\\Auth\\User')) {
            $baseClasses[] = '\\Illuminate\\Foundation\\Auth\\User';
        }

        return in_array(get_parent_class(static::class), $baseClasses);
    }

    /**
     * @return array
     */
    protected static function getBaseClasses()
    {
        return [];
    }

    /**
     * @param array $attributes
     * @param null $connection
     * @return mixed
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;
        $childClass = array_get($attributes, static::getInheritanceField(), null);

        if ($childClass && $childClass != static::class) {
            static::guardAgainstChildClassNotFound($childClass);

            $model = new $childClass($attributes);
            return $model->newFromBuilder($attributes, $connection);
        }

        return parent::newFromBuilder($attributes, $connection);
    }

    /**
     * Adds the single table inheritance global scope for all child models.
     */
    public static function bootSingleTableInheritance()
    {
        if (! self::isImmediateChildOfEloquent()) {
            static::addGlobalScope(new SingleTableInheritanceScope(static::getInheritanceField(), static::class));
        }
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes = [])
    {
        $childClass = array_get($attributes, static::getInheritanceField(). null);

        if ($childClass && $childClass != static::class) {
            static::guardAgainstChildClassNotFound($childClass);

            return $childClass::create($attributes);
        }

        return parent::create($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        $field = static::getInheritanceField();
        // Adds the default type when creating child models.
        if (!static::isImmediateChildOfEloquent() && !isset($attributes[$field])) {
            $this->{$field} = static::class;
        }

        return parent::fill($attributes);
    }

    /**
     * @return string
     */
    protected static function getInheritanceField()
    {
        return 'type';
    }

    /**
     * @param string $childClass
     * @throws ChildClassNotFoundException
     */
    protected static function guardAgainstChildClassNotFound($childClass)
    {
        if (!class_exists($childClass)) {
            throw new ChildClassNotFoundException($childClass);
        }
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $field = static::getInheritanceField();
        $childClass = array_get($attributes, $field, null);

        if ($childClass && $childClass != static::class) {
            static::guardAgainstChildClassNotFound($childClass);
            
            return (new $childClass())->newInstance($attributes, $exists);
        }

        return parent::newInstance($attributes, $exists);
    }
}