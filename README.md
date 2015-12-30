A lite-weight implementation of SingleTableInheritance (STI) on Eloquent.

## Introduction

Let's say you have a User model like so:

```php
<?php

namespace App;

class User extends \Eloquent {}

```

And in your application a user can be _admin_ or _employee_, so you have a `type` attribute in the users
table which you constantly check in many places on your application in order to handle these different subtypes.

Wouldn't be great if you could have subtype models of User like so:

```php
<?php

namespace App;

class Employee extends User {}

class Admin extends User {}
```

With this package you can. It allows you to subtype any class in a simple, lite-weight way.

## Configuration

You have to do in your `User` class:

* use the trait
* explicitly define the base table
* (optional, but recommended) add `type` as a fillable field

like so:

```php

namespace App;

use SingleTableInheritance\SingleTableInheritance;

class User extends \Eloquent {
    use SingleTableInheritance;

    protected $table = 'users';

    // optional
    protected $fillable = ['type'];
}

```

Now you are good to go.

### Fetching different model instances

```php
<?php

factory(User::class)->create();
factory(User::class)->create(['type' => Employee::class]);
$users = User::all();
```

This would result in a collection containing one instance of `User` and one instance of `Employee`.

Check the [tests](tests/) folder for more usage examples.