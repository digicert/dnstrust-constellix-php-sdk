<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use JsonSerializable;
use Spatie\Enum\Enum;

/**
 * An abstract class for resource models in the Constellix API.
 *
 * @package Constellix\Client\Models
 * @property-read int $id
 */
abstract class AbstractModel implements AbstractModelInterface, JsonSerializable
{
    /**
     * The ID of the object.
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * A list of properties that have been modified since the object was last saved.
     * @var array
     */
    protected array $changed = [];

    /**
     * The properties of this object.
     * @var array
     */
    protected array $props = [];

    /**
     * The original properties from when the object was instantiated/last loaded from the API.
     * @var array
     */
    protected array $originalProps = [];

    /**
     * A list of properties that are editable on this model.
     * @var array
     */
    protected array $editable = [];

    /**
     * The original data retrieved from the API.
     * @var object|null
     */
    protected ?object $apiData = null;

    /**
     * Allow easy custom initialisation of properties in models.
     */
    protected function setInitialProperties()
    {
        // Do nothing by default
    }

    /**
     * Returns a string representation of the model's class and ID.
     * @return string
     * @throws \ReflectionException
     * @internal
     */
    public function __toString()
    {
        $rClass = new \ReflectionClass($this);
        $modelName = $rClass->getShortName();
        if ($this->id === null) {
            return "{$modelName}:#";
        }
        return "{$modelName}:{$this->id}";
    }

    /**
     * @param object $data
     * @param bool $parse
     * @internal
     */
    public function populateFromApi(object $data, bool $parse = true): void
    {
        $this->apiData = $data;
        unset($data->links);
        if (property_exists($data, 'id')) {
            $this->id = $data->id;
        }
        if ($parse) {
            $this->parseApiData($data);
        }
        $this->originalProps = $this->props;
        $this->changed = [];
    }

    /**
     * Parses the API data and assigns it to properties on this object.
     * @param object $data
     */
    protected function parseApiData(object $data): void
    {
        foreach ($data as $prop => $value) {
            try {
                $this->{$prop} = $value;
            } catch (ReadOnlyPropertyException $ex) {
                $this->props[$prop] = $value;
            }
        }
    }

    /**
     * Generate a representation of the object for sending to the API.
     * @return object
     * @internal
     */
    public function transformForApi(): object
    {
        $obj = $this->jsonSerialize();
        if ($this->id === null) {
            unset($obj->{$this->id});
        }
        // These don't exist
        foreach ($obj as $key => $value) {
            if ($value === null || (is_array($value) && !$value)) {
                unset($obj->$key);
            }
        }
        return $obj;
    }

    /**
     * Returns a JSON serializable representation of the resource.
     * @return mixed|object
     * @internal
     */
    public function jsonSerialize()
    {
        $result = (object)[
            'id' => $this->id,
        ];
        foreach ($this->props as $name => $value) {
            if ($value instanceof \DateTime) {
                $value = $value->format('c');
            }
            if ($value instanceof Enum) {
                $value = $value->value;
            }
            $result->{$name} = $value;
        }
        return $result;
    }

    /**
     * Returns the ID of the object. Since ID is a protected property, this is required for fetching it.
     * @return int|null
     */
    protected function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Magic method to fetch properties for the object. If a get{Name} method exists, it will be called  first,
     * otherwise it will try and fetch it from the properties array.
     * @param $name
     * @return mixed
     * @internal
     */
    public function __get($name)
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        } elseif (array_key_exists($name, $this->props)) {
            return $this->props[$name];
        }
    }

    /**
     * Magic method for setting properties for the object. If a method called set{Name} exists, then it will be called,
     * otherwise if the property is in the props array and is editable, it will be updated.
     *
     * Changes are tracked to allow us to see any changes.
     *
     * @param $name
     * @param $value
     * @throws ReadOnlyPropertyException
     * @internal
     */
    public function __set($name, $value)
    {
        $methodName = 'set' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            $this->{$methodName}($value);
        } elseif (in_array($name, $this->editable)) {
            $this->props[$name] = $value;
            $this->changed[] = $name;
        } elseif (array_key_exists($name, $this->props)) {
            throw new ReadOnlyPropertyException("Unable to set {$name}");
        }
    }
}