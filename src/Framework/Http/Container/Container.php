<?php

namespace Framework\Http\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private $definitions = [];
    private $results = [];

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function get(string $id)
    {
        if (array_key_exists($id, $this->results)) {
            return $this->results[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            if (class_exists($id)) {

                $reflection = new \ReflectionClass($id);
                $arguments = [];

                if (($constructor = $reflection->getConstructor()) !== null) {
                    foreach ($constructor->getParameters() as $parameter) {
                        if ($paramClass = $parameter->getClass()) {
                            $arguments[] = $this->get($paramClass->getName());
                        } elseif ($parameter->isArray()) {
                            $arguments[] = [];
                        } else {
                            if (!$parameter->isDefaultValueAvailable()) {
                                throw new ServiceNotFoundException('Unable to resolve "' . $parameter->getName() . '" in service "' . $id . '"');
                            }

                            $arguments[] = $parameter->getDefaultValue();
                        }
                    }
                }

                $result = $reflection->newInstanceArgs($arguments);

                return $this->results[$id] = $result;
            }

            throw new ServiceNotFoundException('Undefined parameters "' . $id . '"' );
        }

        $definition = $this->definitions[$id];

        if ($definition instanceof \Closure) {
            $this->results[$id] = $definition($this);
        } else {
            $this->results[$id] = $definition;
        }

        return $this->results[$id];
    }

    public function set($id, $value)
    {
        if (array_key_exists($id, $this->results)) {
            unset($this->results[$id]);
        }

        $this->definitions[$id] = $value;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id)
    {
        return array_key_exists($id, $this->definitions) || class_exists($id);
    }
}