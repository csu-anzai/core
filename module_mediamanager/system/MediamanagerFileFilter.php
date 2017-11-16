<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Mediamanager\System;

use Kajona\System\System\FilterBase;
use Kajona\System\System\OrmComparatorEnum;
use Kajona\System\System\OrmCondition;

/**
 * @package module_mediamanager
 * @author christoph.kappestein@artemeon.de
 * @module mediamanager
 * @moduleId _mediamanager_module_id_
 */
class MediamanagerFileFilter extends FilterBase
{
    /**
     * @var boolean
     * @tableColumn file_search_content
     */
    private $bitIndexPending;

    /**
     *@inheritdoc
     */
    protected function getSingleOrmCondition($strAttributeName, $strValue, $strTableColumn, OrmComparatorEnum $enumFilterCompareOperator = null)
    {
        switch ($strAttributeName) {
            case "bitIndexPending":
                if ($this->bitIndexPending === true) {
                    return new OrmCondition(" file_search_content IS NULL OR file_search_content = '' ", []);
                } elseif ($this->bitIndexPending === false) {
                    return new OrmCondition(" file_search_content IS NOT NULL AND file_search_content != '' ", []);
                } else {
                    return null;
                }
                break;
        }

        return parent::getSingleOrmCondition($strAttributeName, $strValue, $strTableColumn, $enumFilterCompareOperator);
    }

    /**
     * @return bool
     */
    public function getBitIndexPending()
    {
        return $this->bitIndexPending;
    }

    /**
     * @param bool $bitIndexPending
     */
    public function setBitIndexPending($bitIndexPending)
    {
        $this->bitIndexPending = $bitIndexPending;
    }
}
