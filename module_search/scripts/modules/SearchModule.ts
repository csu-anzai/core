import axios from 'axios'
import to from 'await-to-js'
import * as toastr from 'toastr'
import { SearchResult, FilterModule } from '../Interfaces/SearchInterfaces'

const SearchModule = {
    namespaced: true,
    state: { searchResults: [],
        dialogIsOpen: false,
        searchQuery: '',
        filterModules: null,
        selectedIds: '',
        fetchingResults: false,
        startDate: '',
        endDate: '',
        selectedUser: ''
    },
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
        SET_FILTER_MODULES (state : any, payload : Array<FilterModule>) {
            state.filterModules = payload
        },
        SET_SELECTED_IDS (state : any, payload : string) {
            state.selectedIds = payload
        },
        RESET_SELECTED_IDS (state : any) {
            state.selectedIds = ''
        },
        SET_FETCHING_RESULTS (state : any, payload : boolean) {
            state.fetchingResults = payload
        },
        SET_START_DATE (state : any, payload : Date) {
            state.startDate = payload
        },
        SET_END_DATE (state : any, payload : Date) {
            state.endDate = payload
        },
        REST_END_DATE (state : any) {
            state.endDate = ''
        },
        REST_START_DATE (state : any) {
            state.startDate = ''
        },
        SET_SELECTED_USER (state : any, payload : string) {
            state.selectedUser = payload
        },
        RESET_SELECTED_USER (state : any) {
            state.selectedUser = ''
        }

    },
    actions: {
        async triggerSearch ({ commit, state }) : Promise<void> {
            commit('SET_FETCHING_RESULTS', true)
            let url = '/xml.php?admin=1&module=search&action=getFilteredSearch'
            if (state.searchQuery !== '') {
                url += '&search_query=' + state.searchQuery
            } if (state.selectedIds !== '') {
                url += '&filtermodules=' + state.selectedIds
            } if (state.startDate !== '') {
                url += '&search_changestartdate=' + state.startDate
            }
            if (state.endDate !== '') {
                url += '&search_changeenddate=' + state.endDate
            }
            if (state.selectedUser !== '') {
                url += '&search_formfilteruser_id=' + state.selectedUser
            }
            const [err, res] = await to(axios.post(KAJONA_WEBPATH + url))
            if (err) {
                toastr.error('Fehler')
            } if (res) {
                commit('SET_SEARCH_RESULTS', res.data)
            }
            commit('SET_FETCHING_RESULTS', false)
        },
        setSearchQuery ({ commit, state }, searchQuery : string) : void {
            if (state.searchQuery.length > searchQuery.length && searchQuery.length < 2 && state.searchResults.length !== 0) {
                commit('RESET_SEARCH_RESULTS')
            }
            commit('SET_SEARCH_QUERY', searchQuery)
        },
        resetSearchResults ({ commit }) : void {
            commit('RESET_SEARCH_RESULTS')
        },
        closeDialog ({ commit }) : void {
            commit('CLOSE_SEARCH_DIALOG')
            commit('RESET_SEARCH_RESULTS')
            commit('REST_SEARCH_QUERY')
            commit('REST_END_DATE')
            commit('REST_START_DATE')
            commit('RESET_SELECTED_IDS')
            // commit('RESET_SELECTED_IDS')
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
                commit('SET_FILTER_MODULES', res.data)
            }
        },
        setSelectedIds ({ commit }, ids: string) : void {
            commit('SET_SELECTED_IDS', ids)
        },
        setSelectedUser ({ commit }, user:string) : void {
            commit('SET_SELECTED_USER', user)
        },
        setStartDate ({ commit }, startDate : Date) : void {
            commit('SET_START_DATE', startDate)
        },
        setEndDate ({ commit }, endDate : Date) : void {
            commit('SET_END_DATE', endDate)
        }
    },
    getters: {}
}

export default SearchModule
