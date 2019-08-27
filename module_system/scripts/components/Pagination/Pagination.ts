import { Component, Mixins, Prop } from 'vue-property-decorator'
import { LangMixin } from 'core/module_system/scripts/kajona/VueMixings'

@Component class Pagination extends Mixins(LangMixin(['commons', 'system'])) {
@Prop({ type: Number, required: true }) total !: number
@Prop({ type: Number, required: true }) currentPage !: number
private current : number = null as number
private mounted () : void {
    this.current = this.currentPage
}
private changePage (page : number) : void{
    if (this.current !== page) {
        this.current = page
        this.$emit('change', this.current)
    }
}
private next () : void {
    if (this.current + 1 <= this.total) {
        this.current = this.current + 1
        this.$emit('change', this.current)
    }
}

private previous () : void {
    if (this.current - 1 >= 1) {
        this.current = this.current - 1
        this.$emit('change', this.current)
    }
}
}

export default Pagination
