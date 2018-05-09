/**
 * (c) 2013-2017 by Kajona, www.kajona.de
 * Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt
 */

/**
 * Form management
 *
 * @module forms
 */
define('forms', ['jquery', 'tooltip', 'router', 'util', 'messaging'], function ($, tooltip, router, util, messaging) {

    /** @exports forms */
    var forms = {};

    /**
     * Hides a field in the form
     *
     * @param objField - my be a jquery field or a id selector
     */
    forms.hideField = function(objField) {
        objField = util.getElement(objField);

        var objFormGroup = objField.is('h3') || objField.is('h4') ? objField : objField.closest('.form-group');

        //1. Hide field
        objFormGroup.slideUp(0);

        //2. Hide hint -> check if previous element has 'form-group' and if <span> with .help-block exists
        var objHintFormGroup = objFormGroup.prev('.form-group');
        if(objHintFormGroup.find('div > span.help-block').length > 0) {
            objHintFormGroup.slideUp(0);
        }
    };

    /**
     * Shows a field in the form
     *
     * @param objField - my be a jquery object or an id selector
     */
    forms.showField = function(objField) {
        objField = util.getElement(objField);

        var objFormGroup = objField.is('h3') || objField.is('h4') ? objField : objField.closest('.form-group');

        //1. Show field
        objFormGroup.slideDown(0);

        //2. Show hint -> check if previous element has 'form-group' and if <span> with .help-block exists
        var objHintFormGroup = objFormGroup.prev('.form-group');
        if(objHintFormGroup.find('div > span.help-block').length > 0) {
            objHintFormGroup.slideDown(0);
        }
    };

    /**
     * Disables a field
     *
     * @param objField - my be a jquery object or an id selector
     */
    forms.setFieldReadOnly = function(objField) {
        objField = util.getElement(objField);

        if (objField.is('input:checkbox') || objField.is('select') || objField.data('datepicker') !== null) {
            objField.prop("disabled", "disabled");
        }
        else {
            objField.attr("readonly", "readonly");
        }
    };

    /**
     * Enables a field
     *
     * @param objField - my be a jquery object or an id selector
     */
    forms.setFieldEditable = function(objField) {
        objField = util.getElement(objField);

        if (objField.is('input:checkbox') || objField.is('select') || objField.data('datepicker') !== null) {
            objField.removeProp("disabled");
        }
        else {
            objField.removeProp("readonly");
        }
    };

    /**
     * Gets the jQuery object
     *
     * @param objField - my be a jquery object or an id selector
     * @deprecated
     */
    forms.getObjField = function (objField) {
        // If objField is already a jQuery object
        return util.getElement(objField);
    };


    forms.initForm = function(strFormid) {
        $('#'+strFormid+' input , #'+strFormid+' select , #'+strFormid+' textarea ').each(function() {
            $(this).attr("data-kajona-initval", $(this).val());
        });
    };

    forms.animateSubmit = function(objForm) {
        //try to get the button currently clicked

        if($(document.activeElement).prop('tagName') == "BUTTON") {
            $(document.activeElement).addClass('processing');
        }
        else {
            $(objForm).find('.savechanges[name=submitbtn]').addClass('processing');
        }
    };



    forms.changeLabel = '';
    forms.changeConfirmation = '';

    /**
     * Adds an onchange listener to the formentry with the passed ID. If the value is changed, a warning is rendered below the field.
     * In addition, a special confirmation may be required to change the field to the new value.
     *
     * @param strElementId
     * @param bitConfirmChange
     */
    forms.addChangelistener = function(strElementId, bitConfirmChange) {
        $('#'+strElementId).on('change', function(objEvent) {
            if($(this).val() != $(this).attr("data-kajona-initval")) {
                if($(this).closest(".form-group").find("div.changeHint").length == 0) {

                    if(bitConfirmChange && bitConfirmChange == true) {
                        var bitResponse = confirm(forms.changeConfirmation);
                        if(!bitResponse) {
                            $(this).val($(this).attr("data-kajona-initval"));
                            objEvent.preventDefault();
                            return;
                        }
                    }

                    forms.addHint(strElementId, forms.changeLabel);
                }
            }
            else {
                forms.removeHint(strElementId);
            }
        });

    };

    /**
     * Renders a hint below an input field
     * @param strElementId
     * @param strHint
     */
    forms.addHint = function(strElementId, strHint) {
        var $objTarget = $('#'+strElementId);
        forms.removeHint(strElementId);
        $objTarget.closest(".form-group").addClass("has-warning");
        $objTarget.closest(".form-group").children("div:first").append($('<div class="changeHint text-warning"><span class="glyphicon glyphicon-warning-sign"></span> ' + strHint + '</div>'));
    };

    /**
     * Removes a hint from an input field
     * @param strElementId
     */
    forms.removeHint = function(strElementId) {
        var $objTarget = $('#'+strElementId);
        $objTarget.closest(".form-group").removeClass("has-warning");
        if($objTarget.closest(".form-group").find("div.changeHint")) {
            $objTarget.closest(".form-group").find("div.changeHint").remove();
        }

    };



    forms.renderMandatoryFields = function(arrFields) {
        for(var i=0; i<arrFields.length; i++) {
            var arrElement = arrFields[i];
            if(arrElement.length == 2) {
                var $objElement = $("#" + arrElement[0]);
                if($objElement) {
                    $objElement.addClass("mandatoryFormElement");
                    $objElement.trigger('kajona.forms.mandatoryAdded');
                }
            }
        }
    };

    forms.renderMissingMandatoryFields = function(arrFields) {
        $(arrFields).each(function(intIndex, strField) {
            var strFieldName = strField[0];
            if($("#"+strFieldName) && !$("#"+strFieldName).hasClass('inputWysiwyg')) {
                $("#"+strFieldName).closest(".form-group").addClass("has-error has-feedback");
                var objNode = $('<span class="glyphicon glyphicon-warning-sign form-control-feedback" aria-hidden="true"></span>');
                $("#"+strFieldName).closest("div:not(.input-group)").append(objNode);
            }
        });
    };

    forms.loadTab = function(strEl, strHref) {
        if (strHref && $("#" + strEl).length > 0) {
            $("#" + strEl).html("");
            $("#" + strEl).addClass("loadingContainer");
            $.get(strHref, function(data) {
                $("#" + strEl).removeClass("loadingContainer");
                $("#" + strEl).html(data);
                tooltip.initTooltip();
            });
        }
    };

    forms.defaultOnSubmit = function (objForm) {
        $(objForm).on('submit', function() {
            return false;
        });
        KAJONA.admin.forms.submittedEl = objForm;
        $(window).off('unload');

        this.animateSubmit(objForm);

        // disable polling on form submit
        messaging.setPollingEnabled(false);

        var $btn = $(document.activeElement);
        if (
            /* there is an activeElement at all */
            $btn.length &&

            /* it's a child of the form */
            $(objForm).has($btn) &&

            /* it's really a submit element */
            $btn.is('button[type="submit"], input[type="submit"], input[type="image"]') &&

            /* it has a "name" attribute */
            $btn.is('[name]')
            ) {
                //name, value
                $(objForm).append($('<input type="hidden">').attr('name', $btn.attr('name')).attr('value', $btn.val()));
                /* access $btn.attr("name") and $btn.val() for data */
        }

        router.registerFormCallback("activate_polling", function(){
            // enable polling after we receive the response of the form
            messaging.setPollingEnabled(true);
        });

        router.loadUrl(objForm.action);

        return false;
    };


    forms.registerUnlockId = function (strId) {
        router.registerLoadCallback("form_unlock", function() {
            $.ajax({url: KAJONA_WEBPATH + '/xml.php?admin=1&module=system&action=unlockRecord&systemid='+strId});
        });
    };

    return forms;

});


