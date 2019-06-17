import { Component, Vue, Prop, Watch } from 'vue-property-decorator'
import uuid from 'uuid/v1'
import MSelect from 'vue-multiselect'
@Component({ components: { MSelect } }) class Multiselect extends Vue {
@Prop({ type: Array, required: true }) options : Array<any>
@Prop({ type: String, required: true }) label : string
private id : string = uuid()
private selectedOptions : Array<string> = []
@Watch('selectedOptions') onChange () : void {
    this.$emit('select', this.selectedOptions)
}
private deleteSelection () : void {
    this.selectedOptions = []
}
}
export default Multiselect
