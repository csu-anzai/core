<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System\Authorization;

use Kajona\Api\System\AuthorizationInterface;
use Slim\Http\Request;

/**
 * Composite authorization service which tries to use different authorization strategies
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.1
 */
class Composite implements AuthorizationInterface
{
    /**
     * @var AuthorizationInterface[]
     */
    private $authorizations;

    public function __construct(AuthorizationInterface...$authorizations)
    {
        $this->authorizations = $authorizations;
    }

    /**
     * @inheritdoc
     */
    public function isAuthorized(Request $request): bool
    {
        foreach ($this->authorizations as $authorization) {
            if ($authorization->isAuthorized($request)) {
                return true;
            }
        }

        return false;
    }
}
