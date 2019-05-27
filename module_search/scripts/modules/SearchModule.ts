import axios from 'axios'
import to from 'await-to-js'
import * as toastr from 'toastr'
const SearchModule = {
    namespaced: true,
    state: { searchResults: [] },
    mutations: {
        /**
         * sets the results of the search in the state for future usage
         * @param state
         * @param payload
         */
        SET_SEARCH_RESULTS (state : any, payload : Array<any>) : void {
            state.searchResults = payload
        }
    },
    actions: {
        /**
         * triggers the search action
         * @param query
         */
        async triggerSearch ({ commit }, searchQuery : String) : Promise<void> {
            const [err, res] = await to(axios.post('/agp-core-project/xml.php?admin=1&module=search&action=SearchXml&asJson=1&search_query=' + searchQuery))
            if (err) {
                toastr.error('error')
            } if (res) {
                commit('SET_SEARCH_RESULTS', res.data)
            }
            console.log(res.data)
        }
    },
    getters: {}
}

export default SearchModule
