import $ from 'jquery'
import { Component, Vue, Watch, Prop } from 'vue-property-decorator'
import uuid from 'uuid/v1'
import { AutocompleteItem } from './AutcompleteInterfaces'
@Component class Autocomplete extends Vue {
    @Prop({ type: String, required: true }) label : string // label of the autocomplete
    @Prop({ type: Array, required: true }) data : Array<AutocompleteItem>
    private input : string = ''
    private listId : string = uuid()
    private inputId : string = uuid()
    private mounted () : void {
        $('#' + this.inputId).autocomplete({
            source: this.data,
            appendTo: '#' + this.listId,
            select: (event, ui) => {
                // $('#' + this.inputId).val(ui.item.title)
                this.$emit('select', ui.item.value)
                // return false
            }
            // focus: (event, ui) => {
            //     $('#' + this.inputId).val(ui.item.title)
            //     return false
            // }
        })
    }

    @Watch('input') onChange () : void {
        // console.log('input : ', this.input)
        // if (this.input.length === 0) {
        //     this.$emit('delete')
        // } else {
        //     this.$emit('input', this.input)
        // }
        this.$emit('input', this.input)
    }
    @Watch('data') onDataChange () : void {
        console.log('data update')
        $('#' + this.inputId).autocomplete({
            source: this.data,
            appendTo: '#' + this.listId
        }).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li></li>')
                .data('ui-autocomplete-item', item)
                .append('<div class=\'ui-autocomplete-item\' >' + item.label + '</div>')
                .appendTo(ul)
        }
    }
    private deleteInput () : void {
        this.input = ''
        this.$emit('delete')
    }
}

export default Autocomplete
