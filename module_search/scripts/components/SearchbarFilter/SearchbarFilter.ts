import { Component, Vue } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import Loader from 'core/module_system/scripts/components/Loader.vue'
@Component({ components: { Loader } }) class SearchbarFilter extends Vue {
    @namespace('SearchModule').Action getFilterModules : any
    @namespace('SearchModule').State filterModules : object
    private filterIsOpen : boolean = false
    // private async mounted () : Promise<void> {
    //     console.log('filterModules : ', this.filterModules)
    //     if (this.filterModules === null) {
    //         await this.getFilterModules()
    //     }
    // }

    private toggleFilter () : void {
        // if (!this.filterIsOpen ) {
        if (this.filterModules === null) {
            this.getFilterModules()
        }
        this.filterIsOpen = !this.filterIsOpen
        // }
        // else {

        // }
    }
}

export default SearchbarFilter
