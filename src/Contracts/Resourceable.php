<?php

namespace Faliure\Resourceable\Contracts;

use Faliure\LaravelCustomBuilder\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

interface Resourceable
{
    public function resource(): JsonResource;

    public static function resources(): ResourceCollection;

    public static function resourcesQuery(): Builder;
}
