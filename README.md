Auto Json Response Bundle
=========================

v1.1.0

A Symfony listener which converts controller result to a appropriate JsonResponse.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2a0c6077-2542-41f9-ac29-c84ef7239771/big.png)](https://insight.sensiolabs.com/projects/2a0c6077-2542-41f9-ac29-c84ef7239771)

[![Latest Stable Version](https://poser.pugx.org/chrisyue/auto-json-response-bundle/v/stable)](https://packagist.org/packages/chrisyue/auto-json-response-bundle)
[![License](https://poser.pugx.org/chrisyue/auto-json-response-bundle/license)](https://packagist.org/packages/chrisyue/auto-json-response-bundle)
[![Build Status](https://travis-ci.org/chrisyue/auto-json-response-bundle.svg?branch=develop)](https://travis-ci.org/chrisyue/auto-json-response-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chrisyue/auto-json-response-bundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/chrisyue/auto-json-response-bundle/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/chrisyue/auto-json-response-bundle/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/chrisyue/auto-json-response-bundle/?branch=develop)
[![StyleCI](https://styleci.io/repos/52212031/shield)](https://styleci.io/repos/52212031)

Features
--------

* Convert `null` to `JsonResponse(null, 204)`
* Convert `$array|$object` to `JsonResponse($array|$normalizedObject)`
* Convert `$array|$object` to `JsonResponse($array|$normalizedObject, 201)` if the method is `POST`

Installation
------------

```
$ composer require chrisyue/auto-json-response-bundle
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

Usage
-----

This bundle will take effect if the route `_format` parameter is set to `json`.

```yaml
# in your route file:
api:
    resource: ...
    defaults:
        _format: json
```

or in your controller file when you use annotation

```php
/**
 * @Route(...)
 */
public function putAction(Response $response, $_format = 'json')
{
    ...

    return $object;
}
```

or any other ways to set the `$_format` to `json`.

This bundle uses Symfony built-in serializer to normalize object, so the serialize feature should be enable if you want to deal with object:

```yaml
# app/config/config.yml
framework:
    # ...
    serializer:
        enabled: true
```

with the power of the built-in serializer, we can do more configuration to meet our needs, like convert camalCase property to snake\_case:

```yaml
# app/config/config.yml
framework:
    serializer:
        enable_annotations: true
        name_converter: serializer.name_converter.camel_case_to_snake_case
```

More information about serialize, just check [symfony official documentation](https://symfony.com/doc/current/cookbook/serializer.html)

After v1.1.0, this bundle support specify default serialization groups:

```yaml
#app/config/config.yml
chrisyue_auto_json_response:
    serializer:
        default_groups:
            - 'group1'
            - 'group2'
```
