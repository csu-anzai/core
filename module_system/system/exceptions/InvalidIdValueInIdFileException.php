<?php

declare(strict_types=1);

namespace Kajona\System\System\Exceptions;

use Kajona\System\System\Exception;
use Throwable;

final class InvalidIdValueInIdFileException extends Exception
{
    public function __construct(Throwable $previousException = null)
    {
        parent::__construct('id value in idFile has invalid format', $previousException);
    }
}