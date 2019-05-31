import axios from 'axios'
import to from 'await-to-js'
import * as toastr from 'toastr'
const SearchModule = {
    namespaced: true,
    state: { searchResults: [], dialogIsOpen: false, searchQuery: '' },
    mutations: {
        SET_SEARCH_RESULTS (state : any, payload : Array<any>) : void {
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
        }

    },
    actions: {
        async triggerSearch ({ commit }, searchQuery : String) : Promise<void> {
            commit('SET_SEARCH_QUERY', searchQuery)
            const [err, res] = await to(axios.post(KAJONA_WEBPATH + '/xml.php?admin=1&module=search&action=SearchXml&asJson=1&search_query=' + searchQuery))
            if (err) {
                toastr.error('error')
            } if (res) {
                commit('SET_SEARCH_RESULTS', res.data)
            }
            console.log(res.data)
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
        }
    },
    getters: {}
}

export default SearchModule
