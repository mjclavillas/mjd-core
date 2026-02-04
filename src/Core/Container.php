<?php
namespace Mark\MjdCore\Core;

use ReflectionClass;
use Exception;

class Container
{
    protected $bindings = [];
    protected $instances = [];

    public function singleton($abstract, $factory) {
        $this->bindings[$abstract] = [
            'resolver' => $factory,
            'singleton' => true
        ];
    }

    public function bind($key, $resolver)
    {
        $this->bindings[$key] = [
            'resolver' => $resolver,
            'singleton' => false
        ];
    }

    public function make($abstract) {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $object = $binding['resolver']($this);

            if ($binding['singleton']) {
                $this->instances[$abstract] = $object;
            }

            return $object;
        }

        return $this->resolve($abstract);
    }

    protected function resolve($class)
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            } else {
                if (!$parameter->isOptional()) {
                    throw new Exception("Cannot resolve primitive dependency: {$parameter->getName()}");
                }
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}