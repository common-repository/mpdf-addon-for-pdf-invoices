<?php

namespace Psr\Log;

if (!class_exists('Psr\Log\InvalidArgumentException')) {
    class InvalidArgumentException extends \InvalidArgumentException
    {
    }
}
