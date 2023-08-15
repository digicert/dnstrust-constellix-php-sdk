<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Exceptions\Client\ReadOnlyPropertyException;
use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use Constellix\Client\Managers\AbstractManager;
use JsonSerializable;
use Spatie\Enum\Enum;

/**
 * An abstract class for resource models in the Constellix API.
 *
 * @package Constellix\Client\Models
 * @property-read int $id
 */
abstract class AbstractModel implements JsonSerializable
{
    /**
     * The ID of the object.
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * A list of properties that have been modified since the object was last saved.
     * @var array<string>
     */
    protected array $changed = [];

    /**
     * The properties of this object.
     * @var array<mixed>
     */
    protected array $props = [];

    /**
     * The properties that we have loaded
     * @var array<string>
     */
    protected array $loadedProps = [];

    /**
     * The original properties from when the object was instantiated/last loaded from the API.
     * @var array<mixed>
     */
    protected array $originalProps = [];

    /**
     * A list of properties that are editable on this model.
     * @var array<string>
     */
    protected array $editable = [];

    /**
     * The original data retrieved from the API.
     * @var ?\stdClass
     */
    protected ?\stdClass $apiData = null;

    /**
     * Have we fully loaded this object?
     * @var bool
     */
    protected bool $fullyLoaded = false;

    /**
     * Allow easy custom initialisation of properties in models.
     */
    protected function setInitialProperties(): void
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
     * @param ?\stdClass $data
     * @param bool $parse
     * @internal
     */
    public function populateFromApi(?\stdClass $data, bool $parse = true): void
    {
        $this->apiData = $data;
        if ($data !== null) {
            unset($data->links);
            if (property_exists($data, 'id')) {
                $this->id = $data->id;
            }
            if ($parse) {
                $this->parseApiData($data);
            }
        }
        $this->originalProps = $this->props;
        $this->changed = [];
    }

    /**
     * Parses the API data and assigns it to properties on this object.
     * @param \stdClass $data
     */
    protected function parseApiData(\stdClass $data): void
    {
        foreach ((array)$data as $prop => $value) {
            if (!in_array($prop, $this->loadedProps)) {
                $this->loadedProps[] = $prop;
            }
            $this->loadedProps[] = $prop;
            try {
                $this->{$prop} = $value;
            } catch (ReadOnlyPropertyException $ex) {
                $this->props[$prop] = $value;
            }
        }
    }

    /**
     * Generate a representation of the object for sending to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        $obj = $this->jsonSerialize();
        if ($this->id === null) {
            unset($obj->{$this->id});
        }
        // These don't exist
        foreach ((array)$obj as $key => $value) {
            if ($value === null || (is_array($value) && !$value)) {
                unset($obj->$key);
            }
        }
        return $obj;
    }

    /**
     * Returns a JSON serializable representation of the resource.
     * @return \stdClass
     * @internal
     */
    public function jsonSerialize(): \stdClass
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
     * @param string $name
     * @return mixed
     * @internal
     */
    public function __get(string $name): mixed
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        } elseif (array_key_exists($name, $this->props)) {
            if (!in_array($name, $this->loadedProps) && !$this->fullyLoaded) {
                $this->loadFulLObject();
            }
            return $this->props[$name];
        }
        return null;
    }

    /**
     * Magic method for setting properties for the object. If a method called set{Name} exists, then it will be called,
     * otherwise if the property is in the props array and is editable, it will be updated.
     *
     * Changes are tracked to allow us to see any changes.
     *
     * @param string $name
     * @param mixed $value
     * @throws ReadOnlyPropertyException
     * @internal
     */
    public function __set(string $name, mixed $value): void
    {
        $methodName = 'set' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            $this->{$methodName}($value);
        } elseif (in_array($name, $this->editable)) {
            $this->props[$name] = $value;
            if (!in_array($name, $this->loadedProps)) {
                $this->loadedProps[] = $name;
            }
            $this->changed[] = $name;
        } elseif (array_key_exists($name, $this->props)) {
            throw new ReadOnlyPropertyException("Unable to set {$name}");
        }
    }

    /**
     * Load the full object from the API
     * @return void
     */
    protected function loadFullObject(): void
    {
        if ($this->fullyLoaded) {
            return;
        }
        if ($this->id === null) {
            return;
        }
        $this->refresh();
        $this->fullyLoaded = true;
    }

    public function refresh(): void
    {
        // Do nothing by default
    }
}
