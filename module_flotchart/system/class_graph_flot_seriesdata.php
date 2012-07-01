<?php

/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_graph_flot_SeriesData.php 4527 2012-03-07 10:38:46Z sidler $                                             *
********************************************************************************************************/

/**
 * This class could be used to create graphs based on the flot API.
 * Flot renders charts on the client side.
 *
 * @package module_system
 * @since 4.0
 * @author stefan.meyer1@yahoo.de
 */
class class_graph_flot_seriesdata {
    
    private $strLabel = "";
    private $arrayData = array();
    private $strSeriesChartType = "";
    
    /**
    * Constructor
    *
    */
    public function __construct() {
        
    }
    
    public function getStrLabel() {
        return $this->strLabel;
    }
    public function setStrLabel($strLabel) {
        $this->strLabel = $strLabel;
    }
    
    
    public function getArrayData() {
        return $this->arrayData;
    }
    public function setArrayData($arrayData) {
        $this->arrayData = $arrayData;
    }

    
    public function getStrSeriesChartType() {
        return $this->strSeriesChartType;
    }
    public function setStrSeriesChartType($strSeriesChartType) {
        $this->strSeriesChartType = $strSeriesChartType;
    }
    
    
    public function toJSON() {
        $strComma = ",";
        $str = "{";
            $str .= "label:\"".$this->strLabel."\"".$strComma;
            $str .= "data:".json_encode($this->convertToFlotArrayDataStructure($this->arrayData)).$strComma;
            $str .=  $this->strSeriesChartType;
        $str .= "}";
        
        return $str;
    }
    

    //converts the php array to an array for flot
    protected function convertToFlotArrayDataStructure($arrayData) {
        //return $arrayData;
        //pie + line
        $arrTempTemp = array();
        $i = 0;
        foreach($arrayData as $intKey => $objValue) {
            $arrTemp = array();
            $arrTemp[0] = $i++;
            $arrTemp[1] = $objValue;
            
            $arrTempTemp[]=$arrTemp;
        }
        
        return $arrTempTemp;
    }
}


