import axios from 'axios'
import to from 'await-to-js'
import * as toastr from 'toastr'
import { SearchResult } from '../Interfaces/SearchInterfaces'

const SearchModule = {
    namespaced: true,
    state: { searchResults: [], dialogIsOpen: false, searchQuery: '', filterModules: null },
    mutations: {
        SET_SEARCH_RESULTS (state : any, payload : Array<SearchResult>) : void {
            state.searchResults = payload
        },
        RESET_SEARCH_RESULTS (state :any) : void{
            state.searchResults = []
        },
        CLOSE_SEARCH_DIALOG (state : any) : void {
            state.dialogIsOpen = false
        },
        OPEN_SEARCH_DIALOG (state : any) : void {
            state.dialogIsOpen = true
        },
        SET_SEARCH_QUERY (state :any, payload : String) : void {
            state.searchQuery = payload
        },
        REST_SEARCH_QUERY (state : any) : void {
            state.searchQuery = ''
        },
        SET_FILTER_MODULES (state : any, payload : object) {
            state.filterModules = payload
        }

    },
    actions: {
        async triggerSearch ({ commit }, searchQuery : String) : Promise<void> {
            const [err, res] = await to(axios.post(KAJONA_WEBPATH + '/xml.php?admin=1&module=search&action=getFilteredSearch&search_query=' + searchQuery + '&filtermodules=190007,190001,190002,20170436,20141002,1416141702,20142810,190008,190013,190003,1472721851,0,130,191009,1503387058,20171018'))
            if (err) {
                toastr.error('Fehler')
            } if (res) {
                commit('SET_SEARCH_RESULTS', res.data)
                console.log(res.data)
            }
        },
        resetSearchResults ({ commit }) : void {
            commit('RESET_SEARCH_RESULTS')
        },
        closeDialog ({ commit }) : void {
            commit('CLOSE_SEARCH_DIALOG')
        },
        openDialog ({ commit }) : void {
            commit('OPEN_SEARCH_DIALOG')
        },
        resetSearchQuery ({ commit }) : void {
            commit('REST_SEARCH_QUERY')
        },
        async getFilterModules ({ commit }) : Promise<void> {
            const [err, res] = await to(axios.get(KAJONA_WEBPATH + '/xml.php?admin=1&module=search&action=getModulesForFilter'))
            if (err) {
                toastr.error('Fehler')
            }
            if (res) {
                console.log('modules filter : ', res.data)
                commit('SET_FILTER_MODULES', res.data)
            }
        }
    },
    getters: {}
}

export default SearchModule
