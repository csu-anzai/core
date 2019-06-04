import { Component, Vue, Watch } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import Loader from 'core/module_system/scripts/components/Loader.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { FilterModule } from '../../Interfaces/SearchInterfaces'
@Component({ components: { Loader, Multiselect } }) class SearchbarFilter extends Vue {
    @namespace('SearchModule').Action getFilterModules : any
    @namespace('SearchModule').Action setSelectedIds : any
    @namespace('SearchModule').Action triggerSearch : any
    @namespace('SearchModule').State filterModules : Array<FilterModule>
    @namespace('SearchModule').State searchQuery : string
    @namespace('SearchModule').State selectedIds : string
    private filterIsOpen : boolean = false
    private selectedModules : Array<string> = []

    private toggleFilter () : void {
        if (this.filterModules === null) {
            this.getFilterModules()
        }
        this.filterIsOpen = !this.filterIsOpen
    }
    private get moduleNames () : Array<string> {
        return this.filterModules.map(element => element.module)
    }
    @Watch('selectedModules') onModulesChange () : void {
        var ids = ''
        this.selectedModules.map(selectedFilter => {
            this.filterModules.map(filter => {
                if (filter.module === selectedFilter) {
                    if (ids === '') {
                        ids += filter.id.toString()
                    } else {
                        ids += ',' + filter.id.toString()
                    }
                }
            })
        })
        this.setSelectedIds(ids)
        if (this.searchQuery !== '') {
            this.triggerSearch()
        }
    }
}

export default SearchbarFilter
