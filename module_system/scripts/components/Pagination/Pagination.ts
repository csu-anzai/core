import { Component, Mixins, Prop } from 'vue-property-decorator'
import { BPagination } from 'bootstrap-vue'
import { LangMixin } from 'core/module_system/scripts/kajona/VueMixings'

@Component({ components: { BPagination } }) class Pagination extends Mixins(LangMixin(['commons'])) {
@Prop({ type: Number, required: true }) total !: number
@Prop({ type: Number, required: true }) currentPage !: number

private onChange (page : number) : void {
    this.$emit('change', page)
}
}

export default Pagination
