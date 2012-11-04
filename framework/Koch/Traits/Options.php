<?php
namespace Koch\Traits;

trait Options
{
    private $options = [];

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function getOption($key)
    {
        if (false === isset($this->options[$key])) {
            throw new InvalidArgumentException("Option {$key} does not exist.");
        }

        return $this->options[$key];
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
