<?php
/*"******************************************************************************************************
*   (c) 2007-2017 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;


/**
 * Provides a default implementation of VersionableInterface.
 * May be used to reduce duplicate code
 *
 * @author sidler@mulchprod.de
 * @since 6.5
 * @see VersionableInterface
 */
trait VersionableDefaultImplTrait
{

    /**
     * Returns a human readable name of the action stored with the changeset.
     *
     * @param string $strAction the technical actionname
     *
     * @return string the human readable name
     */
    public function getVersionActionName($strAction)
    {
        return $strAction;
    }

    /**
     * Returns a human readable name of the property-name stored with the changeset.
     *
     * @param string $strProperty the technical property-name
     *
     * @return string the human readable name
     */
    public function getVersionPropertyName($strProperty)
    {
        return SystemChangelogRenderer::renderPropertyName($this, $strProperty);
    }

    /**
     * Renders a stored value. Allows the class to modify the value to display, e.g. to
     * replace a timestamp by a readable string.
     *
     * @param string $strProperty
     * @param string $strValue
     *
     * @return string
     */
    public function renderVersionValue($strProperty, $strValue)
    {
        return SystemChangelogRenderer::renderValue($this, $strProperty, $strValue);
    }

    /**
     * Will be removed in future releases!
     * @return string
     * @deprecated
     */
    public function getVersionRecordName()
    {
        return "Method no longer supported";
    }
}
