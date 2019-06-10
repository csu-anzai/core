import $ from 'jquery'
import { Component, Vue, Watch, Prop } from 'vue-property-decorator'
import uuid from 'uuid/v1'
import { watch } from 'fs'

@Component class Autocomplete extends Vue {
    @Prop({ type: String, required: true }) jsonKey : string // name of key : needed to map the correct key in the autocomplete menu
    @Prop({ type: String, required: true }) label : string // label of the autocomplete
    @Prop({ type: Array, required: true }) data : Array<any> // data to display in the results list
    private input : string = ''
    private listId : string = uuid()
    private inputId : string = uuid()
    private mounted () : void {
        $('#' + this.inputId).autocomplete()
    }

    @Watch('input') onChange () : void {
        this.$emit('input', this.input)
        $('#' + this.inputId).autocomplete({
            source: this.data,
            appendTo: '#' + this.listId,
            select: (event, ui) => {
                this.$emit('select', ui.item)
            }
        }).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li></li>')
                .data('ui-autocomplete-item', item)
                .append('<div class=\'ui-autocomplete-item\' >' + item.icon + item[this.jsonKey] + '</div>')
                .appendTo(ul)
        }.bind(this)
    }
    @Watch('data') onDataChange () : void {
        $('#' + this.inputId).autocomplete({
            source: this.data
        })
    }
    private deleteInput () : void {
        this.input = ''
        this.$emit('delete')
    }
}
export default Autocomplete
