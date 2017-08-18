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
        return self::writeMermaid($objFlow, $objHighlite);
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
