<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Flow\System;

use Kajona\System\System\Link;

/**
 * FlowGraphWriter
 *
 * @author christoph.kappestein@artemeon.de
 */
class FlowGraphWriter
{
    /**
     * Generates a mermaid graph definition of the flow object
     *
     * @param FlowConfig $objFlow
     * @param mixed $objHighlite
     * @return string
     */
    public static function write(FlowConfig $objFlow, $objHighlite = null)
    {
        return self::writeCytoscape($objFlow, $objHighlite);
    }

    /**
     * @param FlowConfig $objFlow
     * @param FlowStatus|FlowTransition $objHighlite
     * @return string
     */
    private static function writeCytoscape(FlowConfig $objFlow, $objHighlite)
    {
        $arrStatus = $objFlow->getArrStatus();

        // sort status
        usort($arrStatus, function(FlowStatus $objA, FlowStatus $objB){
            if ($objA->getIntIndex() == 1) {
                return 1;
            }
            if ($objA->getIntIndex() == $objB->getIntIndex()) {
                return 0;
            }
            return ($objA->getIntIndex() < $objB->getIntIndex()) ? -1 : 1;
        });

        $arrNodes = [];
        foreach ($arrStatus as $objStatus) {
            $strBgColor = "#fff";
            $strBorder = "solid";
            if ($objHighlite instanceof FlowStatus && $objHighlite->getSystemid() == $objStatus->getSystemid()) {
                $strBgColor = "#eee";
                $strBorder = "dashed";
            } elseif ($objHighlite instanceof FlowTransition && $objHighlite->getParentStatus()->getSystemid() == $objStatus->getSystemid()) {
                $strBgColor = "#eee";
                $strBorder = "dashed";
            }

            $arrNodes[] = [
                'data' => [
                    'id' => $objStatus->getSystemid(),
                    'name' => $objStatus->getStrName(),
                    'color' => $objStatus->getStrColor(),
                    'bgcolor' => $strBgColor,
                    'border' => $strBorder,
                ],
                'grabbable' => false
            ];
        }

        $arrTrans = [];

        foreach ($arrStatus as $objStatus) {
            /** @var FlowStatus $objStatus */
            $arrTransitions = $objStatus->getArrTransitions();
            foreach ($arrTransitions as $objTransition) {
                if (!$objTransition->isVisible()) {
                    continue;
                }

                /** @var $objTransition FlowTransition */
                $objParentStatus = $objTransition->getParentStatus();
                $objTargetStatus = $objTransition->getTargetStatus();

                $arrTrans[] = [
                    'data' => [
                        'id' => $objTransition->getSystemid(),
                        'source' => $objParentStatus->getSystemid(),
                        'target' => $objTargetStatus->getSystemid(),
                    ]
                ];
            }
        }

        $strNodes = json_encode($arrNodes);
        $strTransitions = json_encode($arrTrans);

        $strTmpSystemId = generateSystemid();
        $strLinkTransition = Link::getLinkAdminHref("flow", "listTransition", "&systemid=" . $strTmpSystemId);

        $strLayout = json_encode([
            'name' => 'dagre',
            'fit' => false,
        ]);

        /*
        $strLayout = json_encode([
            'name' => 'grid',
            'rows' => 4
        ]);
        */

        return <<<HTML
<div id='flow-graph' style='position:absolute;width:90%;height:1000px;border:1px solid #999;'></div>
<script type="text/javascript">
    require(['cytoscape', 'cytoscape-dagre', 'dagre'], function(cytoscape, cd, dagre){
        
        cd(cytoscape, dagre);

        var cy = cytoscape({
          container: document.getElementById('flow-graph'),
          style: [{
            selector: 'node',
            style: {
              'font-size': '14',
              'label': 'data(name)',
              'text-valign': 'center',
              'shape': 'roundrectangle',
              'width': '180',
              'height': '35',
              'border-width': '2',
              'border-style': 'data(border)',
              'border-color': 'data(color)',
              'background-color': 'data(bgcolor)'
            }
           }, {
            selector: 'edge',
            style: {
              'width': 4,
              'target-arrow-shape': 'triangle',
              'line-color': '#ddd',
              'target-arrow-color': '#ddd',
              'curve-style': 'bezier',
              'control-point-step-size': 40
            }
          }],
          elements: {
            nodes: {$strNodes}, 
            edges: {$strTransitions}
          },
          layout: {$strLayout},
          zoom: 1,
          pan: { x: ($('#flow-graph').innerWidth() / 2) - 90, y: 40 },
          boxSelectionEnabled: false,
          autounselectify: true,
          zoomingEnabled: true,
          userZoomingEnabled: false,
          panningEnabled: true,
          userPanningEnabled: true
        });

        /*
        cy.$('node').on('click', function(e){
          var ele = e.target;
          location.href = "{$strLinkTransition}".replace('{$strTmpSystemId}', ele.id());
        });
        */
    });
</script>
HTML;
    }

    private static function writeMermaid(FlowConfig $objFlow, $objHighlite = null)
    {
        $arrStatus = $objFlow->getArrStatus();

        // sort status
        usort($arrStatus, function(FlowStatus $objA, FlowStatus $objB){
            if ($objA->getIntIndex() == 1) {
                return 1;
            }
            if ($objA->getIntIndex() == $objB->getIntIndex()) {
                return 0;
            }
            return ($objA->getIntIndex() < $objB->getIntIndex()) ? -1 : 1;
        });

        $arrList = array("graph TD;");

        foreach ($arrStatus as $objStatus) {
            /** @var FlowStatus $objStatus */
            $arrTransitions = $objStatus->getArrTransitions();
            foreach ($arrTransitions as $objTransition) {
                /** @var $objTransition FlowTransition */
                $objTargetStatus = $objTransition->getTargetStatus();
                if ($objTargetStatus instanceof FlowStatus) {
                    $strLineStart = $objTransition->isVisible() ? "--" : "-.";
                    $strLineEnd = $objTransition->isVisible() ? "--" : ".-";
                    $arrList[] = $objStatus->getStrSystemid() . "[" . $objStatus->getStrName() . "]{$strLineStart} <span data-" . $objTransition->getSystemid() . ">______</span> {$strLineEnd}>" . $objTargetStatus->getSystemid() . "[" . $objTargetStatus->getStrName() . "];";
                }
            }
            $arrList["style ".$objStatus->getSystemid()] = "style {$objStatus->getSystemid()} fill:#f9f9f9,stroke:{$objStatus->getStrIconColor()},stroke-width:1px;";
        }

        if ($objHighlite instanceof FlowStatus) {
            $arrList["style ".$objHighlite->getSystemid()] = "style {$objHighlite->getSystemid()} fill:#f9f9f9,stroke:{$objHighlite->getStrIconColor()},stroke-width:3px;";
        } elseif ($objHighlite instanceof FlowTransition) {
            $arrList["style ".$objHighlite->getParentStatus()->getSystemid()] = "style {$objHighlite->getParentStatus()->getSystemid()} fill:#f9f9f9,stroke:{$objHighlite->getStrIconColor()},stroke-width:3px;";
        }

        $strGraph = implode("\n", $arrList);

        $strTmpSystemId = generateSystemid();
        $strLinkTransition = Link::getLinkAdminHref("flow", "listTransition", "&systemid=" . $strTmpSystemId);
        $strLinkTransitionAction = Link::getLinkAdminHref("flow", "listTransitionAction", "&systemid=" . $strTmpSystemId);
        $strLinkTransitionCondition = Link::getLinkAdminHref("flow", "listTransitionCondition", "&systemid=" . $strTmpSystemId);

        return <<<HTML
<div id='flow-graph' class='mermaid' style='color:#fff;'>{$strGraph}</div>
<script type="text/javascript">
    var callback = function(statusId) {
        location.href = "{$strLinkTransition}".replace('{$strTmpSystemId}', statusId);
    };

    require(['mermaid', 'loader', 'jquery'], function(mermaid, loader, $){
        loader.loadFile(["/core/module_flow/scripts/mermaid/mermaid.forest.css"], function(){
            mermaid.init(undefined, $("#flow-graph"));

            $('div > span.edgeLabel > span').each(function(){
                var data = $(this).data();
                var transitionId;
                for (var key in data) {
                    transitionId = key;
                }

                var actionLink = "{$strLinkTransitionAction}".replace('{$strTmpSystemId}', transitionId);
                var conditionLink = "{$strLinkTransitionCondition}".replace('{$strTmpSystemId}', transitionId);

                $(this).html('<a href="' + actionLink + '"><i class="kj-icon fa fa-play-circle-o"></i></a> <a href="' + conditionLink + '"><i class="kj-icon fa fa-table"></i></a');
            });

            $('.node').on('click', function(){
                var statusId = $(this).attr('id');
                location.href = "{$strLinkTransition}".replace('{$strTmpSystemId}', statusId);
            });
            
            $('.node div').css('cursor', 'pointer');
        });
    });
</script>
<style type="text/css">
.mermaid .label {
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-weight: normal;
    font-size: 13px;
}

</style> 
HTML;
    }
}
