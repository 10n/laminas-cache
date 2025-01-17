# ClassCache

## Deprecation

> ### Deprecated
>
> The `ClassCache` pattern is deprecated as of v2.12 and will be removed in v3.0.

## Quick Start

```php
use Laminas\Cache\Pattern\ClassCache;
use Laminas\Cache\Pattern\PatternOptions;

$classCache = new ClassCache(
    $storage,
    new PatternOptions([
        'class' => 'MyClass',
    ])
);
```

> ### Storage Adapter
>
> The `$storage` adapter can be any adapter which implements the `StorageInterface`. Check out the [Pattern Quick Start](./intro.md#quick-start)-Section for a standard adapter which can be used here.

## Configuration Options

Option | Data Type | Default Value | Description
------ | --------- | ------------- | -----------
`storage` | `string\|array\|Laminas\Cache\Storage\StorageInterface` | none | **deprecated** Adapter used for reading and writing cached data.
`class` | `string` | none | Name of the class for which to cache method output.
`cache_output` | `bool` | `true` | Whether or not to cache method output.
`cache_by_default` | `bool` | `true` | Cache all method calls by default.
`class_cache_methods` | `array` | `[]` | List of methods to cache (if `cache_by_default` is disabled).
`class_non_cache_methods` | `array` | `[]` | List of methods to omit from caching (if `cache_by_default` is enabled).

## Examples

### Caching of Import Feeds

```php
use Laminas\Cache\Pattern\ClassCache;
use Laminas\Cache\Pattern\PatternOptions;
use \Laminas\Feed\Reader\Reader;

$cachedFeedReader = new ClassCache(
    $storage,
    new PatternOptions([
        'class' => Reader::class,
        
        // The feed reader doesn't output anything,
        // so the output doesn't need to be caught and cached:
        'cache_output' => false,
    ])
);

$feed = $cachedFeedReader->call("import", ['https://github.com/laminas/laminas-cache/releases.atom']);
// or
$feed = $cachedFeedReader->import('https://github.com/laminas/laminas-cache/releases.atom');
```

## Available Methods

In addition to the methods defined in `PatternInterface` and the `StorageCapableInterface`, this implementation
exposes the following methods.

```php
namespace Laminas\Cache\Pattern;

use Laminas\Cache\Exception;

class ClassCache extends CallbackCache
{
    /**
     * Call and cache a class method
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Exception\RuntimeException
     * @throws \Exception
     */
    public function call($method, array $args = []);

    /**
     * Intercept method overloading; proxies to call().
     *
     * @param  string $method  Method name to call
     * @param  array  $args    Method arguments
     * @return mixed
     * @throws Exception\RuntimeException
     * @throws \Exception
     */
    public function __call($method, array $args)
    {
        return $this->call($method, $args);
    }

    /**
     * Generate a unique key in base of a key representing the callback part
     * and a key representing the arguments part.
     *
     * @param  string     $method  The method
     * @param  array      $args    Callback arguments
     * @return string
     * @throws Exception\RuntimeException
     */
    public function generateKey($method, array $args = []);

    /**
     * Property overloading: set a static property.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     * @see   http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $class = $this->getOptions()->getClass();
        $class::$name = $value;
    }

    /**
     * Property overloading: get a static property.
     *
     * @param  string $name
     * @return mixed
     * @see    http://php.net/manual/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        $class = $this->getOptions()->getClass();
        return $class::$name;
    }

    /**
     * Property overloading: does the named static property exist?
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $class = $this->getOptions()->getClass();
        return isset($class::$name);
    }

    /**
     * Property overloading: unset a static property.
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $class = $this->getOptions()->getClass();
        unset($class::$name);
    }
}
```
