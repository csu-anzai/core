import $ from 'jquery'
import { Component, Vue, Prop } from 'vue-property-decorator'
import uuid from 'uuid//v1'
import Util from 'core/module_system/scripts/kajona/Util'
@Component class Datepicker extends Vue {
@Prop({ type: String, required: true }) label : string
private id : string = uuid()
private mounted () : void {
    var input = $('#' + this.id).datepicker({
        format: Util.transformDateFormat(<string> this.$i18n.t('system.dateStyleShort'), 'bootstrap-datepicker'),
        weekStart: 1,
        autoclose: true,
        language: KAJONA_LANGUAGE,
        todayHighlight: true,
        todayBtn: 'linked',
        daysOfWeekHighlighted: '0,6',
        calendarWeeks: true
    }).on('changeDate', this.onDateChange)
}
private onDateChange (e : DatepickerEventObject) : void {
    this.$emit('change', $('#' + this.id).val())
}
}
export default Datepicker
