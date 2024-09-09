<?php

namespace k1lib\app;

use k1lib\app;

class config
{
    protected app $app;

    protected string $set_name;

    protected array $options = [];

    function __construct(string $set_name)
    {
        $this->set_name = $set_name;        
    }

    function add_option(string $key, string | array | config | null $value): self
    {
        if (array_key_exists($key, $this->options)) {
            trigger_error("The option '$key' already exist", E_USER_ERROR);
        } else {
            $this->options[$key] = $value;
        }
        return $this;
    }

    function get_option(string $key): string | array | config
    {
        if (!array_key_exists($key, $this->options)) {
            trigger_error("The option '$key' do not exist", E_USER_ERROR);
        }
        return $this->options[$key];
    }
    function set_option(string $key, string | array | config $value): self
    {
        if (!array_key_exists($key, $this->options)) {
            trigger_error("The option '$key' do not exist", E_USER_ERROR);
        } else {
            $this->options[$key] = $value;
        }
        return $this;
    }
}
