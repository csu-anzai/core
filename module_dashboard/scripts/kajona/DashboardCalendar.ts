import $ from 'jquery'
import 'fullcalendar'
import WorkingIndicator from '../../../module_system/scripts/kajona/WorkingIndicator'
import Tooltip from '../../../module_system/scripts/kajona/Tooltip'
import Loader from '../../../module_system/scripts/kajona/Loader'
import Ajax from "core/module_system/scripts/kajona/Ajax";
import DialogHelper from "core/module_v4skin/scripts/kajona/DialogHelper";
import Lang from "core/module_system/scripts/kajona/Lang";

class DashboardCalendar {
    public static init () {
        $('#dashboard-calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: ''
            },
            editable: false,
            // theme: false,
            locale: KAJONA_LANGUAGE,
            eventLimit: true,
            events:
                KAJONA_WEBPATH +
                '/xml.php?admin=1&module=dashboard&action=getCalendarEvents',
            eventRender: function (event, el) {
                Tooltip.addTooltip(el, event.tooltip)
                if (event.icon) {
                    el.find('span.fc-title').prepend(event.icon + ' ')
                }
            },
            loading: function (isLoading) {
                if (isLoading) {
                    WorkingIndicator.start()
                } else {
                    WorkingIndicator.stop()
                }
            }
        })
        $('.fc-button-group')
            .removeClass()
            .addClass('btn-group')
        $('.fc-button')
            .removeClass()
            .addClass('btn btn-default')
    }

    public static getICalendarURL () {
        Ajax.genericAjaxCall(
            'dashboard',
            'apiGetOrCreateICalUrl',
            {},
            function (data: any) {
                if (data) {
                    var modalContent =
                        '<div class="input-group">' +
                        '<input type="text" class="form-control" value="' +
                        data.url +
                        '">' +
                        '<span class="input-group-btn">' +
                        '<button class="btn btn-default copy-btn" type="button" title="" onclick="Util.copyTextToClipboard(\'' +
                        data.url +
                        '\')">' +
                        "<i class='kj-icon fa fa-clipboard'>" +
                        '</button>' +
                        '</span>' +
                        '</div>'

                    DialogHelper.showInfoModal('', modalContent)

                    Lang.fetchSingleProperty(
                        'system',
                        'copy_to_clipboard',
                        function (value: string) {
                            $('.copy-btn').attr('title', value)
                        }
                    )

                    Lang.fetchSingleProperty(
                        'system',
                        'copy_page_url',
                        function (value: string) {
                            $('#jsDialog_0_title').text(value)
                        }
                    )
                }
            },
            null,
            null,
            null,
            'json'
        )
    }
}
;(<any>window).DashboardCalendar = DashboardCalendar
export default DashboardCalendar
