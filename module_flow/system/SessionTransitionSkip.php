<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\System\Model;
use Kajona\System\System\Session;

/**
 * Through this class it is possible to mark specific status as skipped. The flow manager then ignores transitions
 * which have as target status the skipped flag. This is useful in case a user returns from a status and dont want to
 * receive a new notification directly again
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class SessionTransitionSkip
{
    /**
     * Returns whether a specific model and status should be skipped
     *
     * @param Model $model
     * @param FlowTransition $transition
     * @return bool
     */
    public static function shouldSkip(Model $model, FlowStatus $status)
    {
        return Session::getInstance()->getSession(self::getSessionKey($model, $status));
    }

    /**
     * Marks a specific model and status as skipped
     *
     * @param Model $model
     * @param FlowTransition $transition
     */
    public static function markSkip(Model $model, FlowStatus $status)
    {
        Session::getInstance()->setSession(self::getSessionKey($model, $status), true);
    }

    /**
     * Removes a skip flag for a specific model and status
     *
     * @param Model $model
     * @param FlowStatus $status
     */
    public static function removeSkip(Model $model, FlowStatus $status)
    {
        Session::getInstance()->sessionUnset(self::getSessionKey($model, $status));
    }

    /**
     * Generates a unique session key for a model and status combination
     *
     * @param Model $model
     * @param FlowStatus $status
     * @return string
     */
    private static function getSessionKey(Model $model, FlowStatus $status)
    {
        return self::class . '_'. $model->getSystemid() . '_' . $status->getSystemid();
    }
}
