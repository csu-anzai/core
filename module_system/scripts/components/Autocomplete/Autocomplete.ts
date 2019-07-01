import $ from 'jquery'
import { Component, Vue, Watch, Prop } from 'vue-property-decorator'
import uuid from 'uuid/v1'
import { AutocompleteItem } from './AutcompleteInterfaces'
import Tooltip from '../../kajona/Tooltip'
@Component class Autocomplete extends Vue {
    @Prop({ type: String, required: true }) label : string // label of the autocomplete
    @Prop({ type: Array, required: true }) data : Array<AutocompleteItem> // data to display
    @Prop({ type: Boolean, required: false }) loading : boolean // property for the loading animation
    @Prop({ type: String, required: false }) tooltip : string // Text to display onHover over the action button
    private input : string = ''
    private listId : string = uuid()
    private inputId : string = uuid()
    private inputIconId : string = uuid()
    private actionBtnId : string = uuid()
    private mounted () : void {
        if (this.tooltip) {
            Tooltip.addTooltip($('#' + this.actionBtnId), this.tooltip)
        }
        $('#' + this.inputId).autocomplete({
            source: this.data,
            appendTo: '#' + this.listId,
            select: (event : any, ui : any) => {
                $('#' + this.inputId).val(ui.item.title)
                this.$emit('select', ui.item.value)
                return false
            },
            focus: (event : any, ui : any) => {
                $('#' + this.inputId).val(ui.item.title)
                return false
            }
        })
    }
    @Watch('loading') onLoadingChange () : void {
        let icon = document.getElementById(this.inputIconId)
        if (this.loading) {
            icon.classList.remove('fa-keyboard-o')
            icon.classList.add('fa-spinner')
            icon.classList.add('fa-spin')
        } else {
            icon.classList.remove('fa-spinner')
            icon.classList.remove('fa-spin')
            icon.classList.add('fa-keyboard-o')
        }
    }
    @Watch('input') onChange () : void {
        if (this.input.length === 0) {
            this.$emit('delete')
        } else {
            this.$emit('input', this.input)
        }
    }
    @Watch('data') onDataChange () : void {
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
