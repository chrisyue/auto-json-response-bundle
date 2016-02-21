# auto-json-response
A Symfony listener which converts controller returned data to a appropriate JsonResponse.

## Feature
* Convert `null` (or no `return`) to `Response(null, 204)`
* Convert `$array` to `JsonResponse($array)`
* Convert `$array` to `JsonResponse($array, 201) if the method is post`

## Installation
```
$ composer require 'chrisyue/auto-json-response:dev-master'
```

```php
// AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Chrisyue\Bundle\AutoJsonResponseBundle\ChrisyueAutoJsonResponseBundle(),
    );
}
```

## Usage
After installation, this bundle will take effect if the route `_format` parameter is set to `json`.
