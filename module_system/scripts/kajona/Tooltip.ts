///<reference path="../../../_buildfiles/jstests/definitions/kajona.d.ts" />
///<amd-module name="tooltip"/>

import * as $ from "jquery";
import "qtip";

/**
 * Common tooltips
 */
class Tooltip {

    public static initTooltip() {

        $('*[rel=tooltip][title!=""]').qtip({
            style: {
                classes: 'qtip-bootstrap'
            }
        });

        //tag tooltips
        $('*[rel=tagtooltip][title!=""]').each( function() {
            $(this).qtip({
                style: {
                    classes: 'qtip-bootstrap'
                },
                content: {
                    text: $(this).attr("title")+"<div id='tags_"+$(this).data('systemid')+"' data-systemid='"+$(this).data('systemid')+"'></div>"
                },
                events: {
                    render: function(event, api) {
                        /*
                        require(['tags'], function(tags) {
                            tags.loadTagTooltipContent($(api.elements.content).find('div').data('systemid'), "", $(api.elements.content).find('div').attr('id'));
                        })
                        */
                    }
                }
            });
        })
    };

    public static addTooltip(objElement: any, strText: string) {
        if(strText) {
            $(objElement).qtip({
                style: {
                    classes: 'qtip-bootstrap'
                },
                content : {
                    text: strText
                }
            });
        }
        else {
            $(objElement).qtip({
                style: {
                    classes: 'qtip-bootstrap'
                }
            });
        }
    };

    public static removeTooltip(objElement: any) {
        $(objElement).qtip('destroy');
    };

}

export = Tooltip
