<?php

declare(strict_types=1);

namespace AdventOfCode\Exception;

class NotImplementedException extends \Exception
{
    public function __construct(string $message = 'Not implemented yet.')
    {
        parent::__construct($message);
    }
}
