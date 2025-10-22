# Server-side adapter for Symfony 7 and Inertia.js v2

Heavily inspired by the [Laravel adapter](https://github.com/inertiajs/inertia-laravel) and [official Symfony bundle](https://github.com/SkipTheDragon/inertia-bundle).

## Installation

1. Add the required repository configuration to `composer.json`

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/webwingscz/inertia-bundle.git"
    }
  ]
}
```

2. Install the composer package

```shell
composer require webwingscz/inertia-bundle
```


## Features

State of features that require backend support.

### Supported

#### The basics

* [Pages](https://inertiajs.com/pages)
  * Customizable page component name resolution
  * Customizable page component existence validation
* [Responses](https://inertiajs.com/responses)
  * ProvidesInertiaProperties interface
    * Only on global level using `InertiaPropProviderInterface` 
  * Root template data
* [Redirects](https://inertiajs.com/redirects)
  * 303 response code
  * External redirects
* [Routing](https://inertiajs.com/routing)
  * Customizing the Page URL
* [Validation](https://inertiajs.com/validation)
    * Sharing errors
    * Error bags

#### Data & props

* [Shared data](https://inertiajs.com/shared-data)
* [Partial reloads](https://inertiajs.com/partial-reloads)
* [Deferred props](https://inertiajs.com/deferred-props)
* [Merging props](https://inertiajs.com/merging-props)
* [Infinite scroll](https://inertiajs.com/infinite-scroll)

#### Security

* [CSRF protection](https://inertiajs.com/csrf-protection)
* [History encryption](https://inertiajs.com/history-encryption)

#### Advanced

* [Asset versioning](https://inertiajs.com/asset-versioning)
* [Error handling](https://inertiajs.com/error-handling)
    * Using `InertiaResponseFactoryInterface`

### Planned

Currently none.

### Unplanned

#### Advanced

* [Server side rendering](https://inertiajs.com/server-side-rendering)