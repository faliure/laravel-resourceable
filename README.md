# faliure/laravel-resourceable

Laravel Resourceable allows you to set a clear relationship between an Eloquent Model and its associated Resource(s): **a Model owns its Resources**.

As of Laravel 9.5, this is still not obvious. There is indeed a default path to store the main Model's Resource, but you still need to do things like `new UserResource($user)`. That is, you're just creating an instance of a certain resource, and passing the model as an argument. You can even pass some other type of Model, or even something that's not an Eloquent Model (within reason) and everything will work.

With this package, we make the relation explicit and change the code to reflect that a JsonResource of a Model belongs to that Model, not the other way around.

---

## Usage

Use the HasResources trait and implement the Resourceable interface in your Models.
```php
# App\Models\User

use Faliure\Resourceable\Contracts\Resourceable;
use Faliure\Resourceable\Traits\HasResources;

class User implements Resourceable
{
    use HasResources;
}
```

Get a single Model's associated Resource:
```php
$user = User::first();

// Without Resourceables
$resource = new UserResource($user);

// With Resourceables
$resource $user->resource();
```

Get all of the model's resources:
```php
// Without Resourceables
$users = User::all();
$resources = UserResource::collection($user);

// With Resourceables
$resources = User::resources();
```

Get a custom Collection of resources of a given Model:
```php
// Without Resourceables
$users = User::where('some', 'constraint')->get();
$resources = UserResource::collection($users);

// With Resourceables
$resources = User::resourcesQuery()->where('some', 'constraint')->get();
```

As you can see, this syntactic sugar puts the Model in the spotlight, and the resource as one of its many associated entities.

---

## Customization

By default, Resourceable assumes your main Model JsonResource follows the standard:

- User's main Resource is `App\Http\Resources\UserModel`

If that's not the case, you may define a property `resourceClass` in your Resourceable Modelm, like so:

```php
class User implements Resourceable
{
    // ...

    protected string $resourceClass = TheResourceClass::class;
}
```

Moreover, you can fetch different kinds of Resources for a model, by passing an optinal `$resourceClass` parameter to any of the methods defined in the interface. Namely:

```php
$resource = $user->resource(SomeOtherResourceType::class);

$resources = User::resources(SomeOtherResourceType::class);

$filteredResources = User::resourcesQuery(SomeOtherResourceType::class)
    ->where('some', 'constraint')
    // ->...
    ->get();
```

It's just as simple as that!


### How does the `resourcesBuilder` work?

The `resourcesBuilder` is a Custom Eloquent Builder that allows you to write Eloquent chains as you normally would, but it modifies the end result to return the associated Resources instead of a Model or a collection of them.

Check [faliure/laravel-custom-builder](https://github.com/faliure/laravel-custom-builder) for more information and other usages.
