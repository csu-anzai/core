<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

/**
 * TokenReader
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class TokenReader
{
    /**
     * Returns the secret token for the project or throws an exception in case a token file was not generated
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getToken(): string
    {
        $tokenFile = _realpath_ . "/project/token.key";

        if (!is_file($tokenFile)) {
            throw new \RuntimeException("Token file not available");
        }

        $token = trim(file_get_contents($tokenFile));

        if (empty($token)) {
            throw new \RuntimeException("No token was specified");
        }

        return $token;
    }
}
