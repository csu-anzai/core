import { Component, Vue, Watch } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import Loader from 'core/module_system/scripts/components/Loader/Loader.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { FilterModule, User } from '../../Interfaces/SearchInterfaces'
import axios from 'axios'
import Datepicker from 'core/module_system/scripts/components/Datepicker/Datepicker.vue'
import Autocomplete from 'core/module_system/scripts/components/Autocomplete/Autocomplete.vue'
@Component({ components: { Loader, Multiselect, Datepicker, Autocomplete } }) class SearchbarFilter extends Vue {
    @namespace('SearchModule').Action getFilterModules : any
    @namespace('SearchModule').Action setSelectedIds : any
    @namespace('SearchModule').Action triggerSearch : any
    @namespace('SearchModule').Action setStartDate: any
    @namespace('SearchModule').Action setEndDate: any
    @namespace('SearchModule').Action setSelectedUser : any
    @namespace('SearchModule').State filterModules : Array<FilterModule>
    @namespace('SearchModule').State searchQuery : string
    @namespace('SearchModule').State selectedIds : string

    private filterIsOpen : boolean = false
    private selectedModules : Array<string> = []
    private users : Array<object> = []
    private userQuery : string = ''

    // @Watch('userQuery') async onChange () : Promise<void> {
    //     const res = await axios.post(KAJONA_WEBPATH + '/xml.php?module=user&action=getUserByFilter&filter=' + this.userQuery + '&user=true&group=false')
    //     this.users = res.data.map(user => user.title)
    // }

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
        if (this.searchQuery.length >= 2) {
            this.triggerSearch()
        }
    }
    private onStartDateChange (startDate : string) : void {
        this.setStartDate(startDate)
        if (this.searchQuery.length >= 2) {
            this.triggerSearch()
        }
    }
    private onEndDateChange (endDate : string) : void {
        this.setEndDate(endDate)
        if (this.searchQuery.length >= 2) {
            this.triggerSearch()
        }
    }
    private onUserSelect (user : User) : void {
        this.setSelectedUser(user.systemid)
        if (this.searchQuery.length >= 2) {
            this.triggerSearch()
        }
    }
}

export default SearchbarFilter
