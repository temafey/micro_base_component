<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

use ArrayAccess;

/**
 * Class Arr
 *
 * @category Micro\BaseComponent
 * @package Setter
 */
class Arr
{
    use MacroableTrait;

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public static function accessible($value): bool
    {
        return \is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return array
     */
    public static function add(array $array, $key, $value): array
    {
        if (null === static::get($array, $key)) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!\is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge(...$results);
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     *
     * @return array
     */
    public static function dot(array $array, $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array  $array
     * @param  array|string  $keys
     *
     * @return array
     */
    public static function except(array $array, $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     *
     * @return bool
     */
    public static function exists(array $array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function first(array $array, callable $callback = null, $default = null)
    {
        if (\is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (\call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function last(array $array, callable $callback = null, $default = null)
    {
        if (null === $callback) {
            return empty($array) ? value($default) : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     *
     * @return array
     */
    public static function flatten(array $array, $depth = INF): array
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (!\is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } else {
                return array_merge($result, static::flatten($item, $depth - 1));
            }
        }, []);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     *
     * @return void
     */
    public static function forget(array &$array, $keys): void
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && \is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return value($default);
        }

        if (null === $key) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function has($array, $keys): bool
    {
        if (null === $keys) {
            return false;
        }

        $keys = (array) $keys;

        if (! $array) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array  $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     *
     * @return array
     */
    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array  $array
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    public static function pluck(array $array, $value, $key = null): array
    {
        $results = [];

        [$value, $key] = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     *
     * @return array
     */
    protected static function explodePluckParameters($value, $key): array
    {
        $value = \is_string($value) ? explode('.', $value) : $value;

        $key = null === $key || \is_array($key) ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     *
     * @return array
     */
    public static function prepend(array $array, $value, $key = null): array
    {
        if (null === $key) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return array
     */
    public static function set(array &$array, $key, $value): array
    {
        if (null === $key) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! \is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function shuffle(array $array): array
    {
        shuffle($array);

        return $array;
    }

    /**
     * Sort the array using the given callback or "dot" notation.
     *
     * @param  array  $array
     * @param  callable|string  $callback
     *
     * @return array
     */
    public static function sort(array $array, $callback): array
    {
        return Collection::make($array)->sortBy($callback)->all();
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     *
     * @return array
     */
    public static function sortRecursive(array $array): array
    {
        foreach ($array as &$value) {
            if (\is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }

        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Filter recursive array1 by values from array2,
     * with default values if key from array2 does not numeric
     * and throw Exception if defualt value does not exists in array2 by key in array1
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function arrayFilterRecursive(array $array1, array $array2): array
    {
        $result = [];

        foreach ($array2 as $key2 => $value2) {

            if (\is_array($value2)) {
                if (!array_key_exists($key2, $array1)) {
                    throw new \Exception(sprintf('Key \'%s\' was not found in the main array!', $key2));
                }
                $result[$key2] = self::arrayFilterRecursive($array1[$key2], $value2);
            } elseif (\is_numeric($key2)) {
                if (!array_key_exists($value2, $array1)) {
                    throw new \Exception(sprintf('Key \'%s\' was not found in the main array!', $value2));
                }
                $result[$value2] = $array1[$value2];
            } else {
                $result[$key2] = array_key_exists($key2, $array1) ? $array1[$key2] : $value2;
            }
        }

        return $result;
    }

    /**
     * Home mande method to do array_diff ~10x faster that PHP built-in.
     *
     * @param array to compare from
     * @param array to compare against
     *
     * @return array containing all the entries from array1 that are not present in array2.
     */
    public static function array_diff(array $array1, array $array2): array
    {
        $diff = [];

        // we don't care about keys anyway + avoids dupes
        foreach ($array1 as $value) {
            $diff[$value] = 1;
        }

        // unset common values
        foreach ($array2 as $value) {
            unset($diff[$value]);
        }

        return array_keys($diff);
    }


    /**
     * Home mande method to do array_intersect ~10x faster that PHP built-in.
     *
     * @param array to compare from
     * @param array to compare against
     *
     * @return array containing all the entries from array1 that are present in array2.
     */
    public static function array_intersect(array $array1, array $array2): array
    {
        $a1 = $a2 = [];

        // we don't care about keys anyway + avoids dupes
        foreach ($array1 as $value) {
            $a1[$value] = $value;
        }
        foreach ($array2 as $value) {
            $a2[$value] = 1;
        }

        // unset different values values
        foreach ($a1 as $value) {
            if (!isset($a2[$value])) {
                unset($a1[$value]);
            }
        }

        return array_keys($a1);
    }
}
