<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Oauth2\System;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class VoidLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
    }
}
