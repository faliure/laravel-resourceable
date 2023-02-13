<?php

namespace Faliure\Resourceable\Traits;

use Faliure\LaravelCustomBuilder\Builder;
use Faliure\Resourceable\Exceptions\NonResourceableModelException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Add direct access to the underlying resource for your Eloquent Models.
 *
 * // Get a collection of the model's resources
 * Model::resources();
 *
 * // Get the resource associated with the model
 * $model->resource()
 *
 * // Run normal eloquent chains, get resources instead of models
 * Model::resourcesQuery()->where(...)->get()
 *
 * @see \Faliure\LaravelCustomBuilder\Builder
 */
trait HasResources
{
    /**
     * Convert the current Model to its corresponding JsonResourse.
     */
    public function resource(?string $resourceClass = null): JsonResource
    {
        $resourceClass ??= static::resourceClass();

        return $resourceClass::make($this);
    }

    /**
     * Create a ResourceCollection with items of this Model.
     */
    public static function resources(?string $resourceClass = null): ResourceCollection
    {
        return static::resourcesQuery($resourceClass)->get();
    }

    /**
     * Get a Builder that converts the results to a JsonResource (for
     * singular results, e.g. first) or a ResourceCollection of them.
     *
     * e.g. User::resourcesQuery()->where(...)->get()
     */
    public static function resourcesQuery(?string $resourceClass = null): Builder
    {
        $resourceClass ??= static::resourceClass();

        return (new Builder(static::class))
            ->setCallback(
                fn ($models) => $resourceClass::collection($models),
                Builder::GET
            )->setCallback(
                fn ($model) => $model?->resource(),
                Builder::FIRST
            );
    }

    /**
     * Array/Json representation based on the corresponding JsonResource
     * definition (may be overriden in the Resourceable Model as needed).
     */
    public function toArray(): array
    {
        return $this->resource()->resolve(new Request());
    }

    /**
     * Resolve the underlying Resource class:
     *   - if the model has a resourceClass static property, use it
     *   - otherwise, default to App\Http\Resources\{ModelName}Resource
     */
    protected static function resourceClass(): string
    {
        $modelClass    = class_basename(static::class);
        $resourceClass = static::$resourceClass
            ?? "\\App\\Http\\Resources\\{$modelClass}Resource";

        if (! class_exists($resourceClass)) {
            throw new NonResourceableModelException(
                'No JsonResource defined for Model ' . class_basename(static::class)
            );
        }

        return $resourceClass;
    }
}
