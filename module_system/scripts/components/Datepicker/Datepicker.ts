import $ from 'jquery'
import { Component, Vue } from 'vue-property-decorator'
import uuid from 'uuid//v1'

@Component class Datepicker extends Vue {
private id : string = uuid()
private mounted () : void {
    var input = $('#' + this.id).datepicker({
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
    this.$emit('changeDate', e)
}
}
export default Datepicker
