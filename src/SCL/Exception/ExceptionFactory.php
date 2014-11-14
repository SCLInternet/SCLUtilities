<?php

namespace SCL\Exception;

trait ExceptionFactory
{
    /**
     * Create an instance of the exception with a formatted message.
     *
     * @param string $message The exception message in sprintf format.
     * @param array $params The sprintf parameters for the message.
     * @param int $code Numeric exception code.
     * @param \Exception $previous The previous exception.
     *
     * @return self
     */
    protected static function create(
        $message,
        array $params = [],
        $code = 0,
        \Exception $previous = null
    ) {
        return new self(vsprintf($message, $params), $code, $previous);
    }
}
