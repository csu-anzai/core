<?php
/*"******************************************************************************************************
*   (c) 2013-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$	                                            *
********************************************************************************************************/

namespace Kajona\System\System\Security;

use Throwable;

/**
 * PasswordExpiredException
 *
 * @package module_system
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class PasswordExpiredException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    protected $strUserId;

    /**
     * @param string $strUserId
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $strUserId, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message);

        $this->strUserId = $strUserId;
    }

    /**
     * @return string
     */
    public function getStrUserId()
    {
        return $this->strUserId;
    }
}
