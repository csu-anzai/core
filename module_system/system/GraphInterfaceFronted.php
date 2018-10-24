<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\System\System;

/**
 * Interface for all based on java script chart-engines.
 * Concrete instances may be returned by GraphFactory.
 * This interface defines only the least subset of methods, so each implementation may
 * provide additional methods.
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.1
 * @see GraphFactory
 * @package module_system
 */
interface GraphInterfaceFronted extends GraphInterface
{

    /**
     * @param bool $bitHorizontal
     * @return mixed
     */
    public function setBarHorizontal(bool $bitHorizontal);

    /**
     * @param bool $bitHideXAxis
     * @return mixed
     */
    public function setHideXAxis(bool $bitHideXAxis = true);

    /**
     * @param bool $bitHideYAxis
     * @return mixed
     */
    public function setHideYAxis(bool $bitHideYAxis = true);

    /**
     * @param bool $bitDrawBorder
     * @return mixed
     */
    public function drawBorder(bool $bitDrawBorder = true);

    /**
     * @param bool $bitIsResizeable
     * @return mixed
     */
    public function setBitIsResizeable(bool $bitIsResizeable = true);

    /**
     * @param bool $bitDownloadLink
     * @return mixed
     */
    public function setBitDownloadLink(bool $bitDownloadLink = true);


    /**
     * @param null $intMin
     * @param null $intMax
     * @param null $intTickInterval
     * @return mixed
     */
    public function setXAxisRange($intMin = null, $intMax = null, $intTickInterval = null);

    /**
     * @param null $intMin
     * @param null $intMax
     * @param null $intTickInterval
     * @return mixed
     */
    public function setYAxisRange($intMin = null, $intMax = null, $intTickInterval = null);

    /**
     * @param bool $autoSize
     * @return mixed
     */
    public function setAsHorizontalInLineStackedChart($autoSize = false);
}
