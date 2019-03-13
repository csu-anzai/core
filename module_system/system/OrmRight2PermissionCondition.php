<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * A orm condition to filter by right2 permission for the currently logged in user
 *
 *
 * @package Kajona\System\System
 * @author stefan.idler@artemeon.de
 * @since 7.2
 */
class OrmRight2PermissionCondition extends OrmCondition
{
    private $strColumn = null;

    /**
     * OrmPermissionCondition constructor.
     *
     * @param string $column the column to query against
     */
    public function __construct($column = "agp_system.system_id")
    {
        parent::__construct("", array());
        $this->strColumn = $column;
    }


    /**
     * @inheritdoc
     */
    public function getStrWhere()
    {
        return <<<SQL
        {$this->strColumn} IN (
          SELECT right2_id
          FROM agp_permissions_right2, agp_user_group, agp_user_kajona_members
          WHERE right2_shortgroup = group_short_id AND group_id = group_member_group_kajona_id AND group_member_user_kajona_id = ? AND right2_id = {$this->strColumn}
        )
SQL;

    }

    /**
     * @inheritdoc
     */
    public function getArrParams()
    {
        return [Session::getInstance()->getUserID()];
    }

}
