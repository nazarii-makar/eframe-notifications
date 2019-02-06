<?php

namespace EFrame\Notifications\Messages;

use EFrame\Amqp\Message;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class AmqpMessage
 * @package EFrame\Notifications\Messages
 */
class AmqpMessage extends Message
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * AmqpMessage constructor.
     *
     * @param string $body
     * @param null   $properties
     */
    public function __construct($body = '', $properties = null)
    {
        parent::__construct(
            $this->prepareBody($body),
            $properties
        );
    }

    /**
     * Sets the message payload
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $this->prepareBody($body);

        return $this;
    }

    /**
     * @param mixed $body
     *
     * @return string
     */
    protected function prepareBody($body = '')
    {
        if ($body instanceof Jsonable) {
            $body = $body->toJson();
        } elseif ($body instanceof Arrayable) {
            $body = json_encode($body->toArray());
        } elseif (is_array($body)) {
            $body = json_encode($body);
        }
        
        return $body;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

}
