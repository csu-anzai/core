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
     * Sets Bar chart as horizontal
     *
     * @param bool $bitHorizontal
     * @return mixed
     */
    public function setBarHorizontal(bool $bitHorizontal);

    /**
     * Hides X Axis if true
     *
     * @param bool $bitHideXAxis
     * @return mixed
     */
    public function setHideXAxis(bool $bitHideXAxis = true);

    /**
     * Hides Y Axis if true
     *
     * @param bool $bitHideYAxis
     * @return mixed
     */
    public function setHideYAxis(bool $bitHideYAxis = true);

    /**
     * Draws borders around the chart
     *
     * @param bool $bitDrawBorder
     * @return mixed
     */
    public function drawBorder(bool $bitDrawBorder = true);

    /**
     * If true sets width as 100%
     *
     * @param bool $bitIsResizeable
     * @return mixed
     */
    public function setBitIsResizeable(bool $bitIsResizeable = true);

    /**
     * Adds a download link pop-up on mouse hover event
     *
     * @param bool $bitDownloadLink
     * @return mixed
     */
    public function setBitDownloadLink(bool $bitDownloadLink = true);


    /**
     * Sets min, max value and step for X axis
     *
     * @param null $intMin
     * @param null $intMax
     * @param null $intTickInterval
     * @return mixed
     */
    public function setXAxisRange($intMin = null, $intMax = null, $intTickInterval = null);

    /**
     * Sets min, max value and step for Y axis
     *
     * @param null $intMin
     * @param null $intMax
     * @param null $intTickInterval
     * @return mixed
     */
    public function setYAxisRange($intMin = null, $intMax = null, $intTickInterval = null);

    /**
     * A set of parameters to make the horizontal stacked inline chart view the same for all chart providers
     *
     * @param bool $autoSize
     * @return mixed
     */
    public function setAsHorizontalInLineStackedChart($autoSize = false);
}
