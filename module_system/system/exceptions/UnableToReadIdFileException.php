<?php

declare(strict_types=1);

namespace Kajona\System\System\Exceptions;

use Kajona\System\System\Exception;
use Throwable;

final class UnableToReadIdFileException extends Exception
{
    public function __construct(string $message, Throwable $previousException = null)
    {
        parent::__construct($message, $previousException);
    }
}