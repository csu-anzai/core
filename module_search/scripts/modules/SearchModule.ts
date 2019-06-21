import axios from 'axios'
import to from 'await-to-js'
import * as toastr from 'toastr'
import { SearchResult, FilterModule, User } from '../Interfaces/SearchInterfaces'

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
        selectedUser: '',
        autoCompleteUsers: []
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
        SET_AUTOCPMPLETE_USERS (state : any, payload : Array<User>) {
            state.autoCompleteUsers = payload
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
            const [err, res] = await to(axios({
                url: '/xml.php',
                method: 'POST',
                params: {
                    module: 'search',
                    action: 'getFilteredSearch',
                    search_query: state.searchQuery !== '' ? state.searchQuery : undefined,
                    filtermodules: state.selectedIds !== '' ? state.selectedIds : undefined,
                    search_changestartdate: state.startDate !== '' ? state.startDate : undefined,
                    search_changeenddate: state.endDate !== '' ? state.endDate : undefined,
                    search_formfilteruser_id: state.selectedUser !== '' ? state.selectedUser : undefined
                }

            }))
            if (err) {
                toastr.error('Fehler')
            } if (res) {
                commit('SET_SEARCH_RESULTS', res.data)
            }
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
            commit('RESET_SELECTED_USER')
        },
        openDialog ({ commit }) : void {
            commit('OPEN_SEARCH_DIALOG')
        },
        resetSearchQuery ({ commit }) : void {
            commit('REST_SEARCH_QUERY')
        },
        async getFilterModules ({ commit }) : Promise<void> {
            const [err, res] = await to(axios({
                url: '/xml.php?',
                method: 'GET',
                params: {
                    module: 'search',
                    action: 'getModulesForFilter'
                }
            }))
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
        async getAutocompleteUsers ({ commit }, userQuery :string) : Promise<void> {
            const [err, res] = await to(axios({
                url: '/xml.php',
                method: 'POST',
                params: {
                    module: 'user',
                    action: 'getUserByFilter',
                    user: true,
                    group: false,
                    filter: userQuery !== '' ? userQuery : undefined
                }
            }))
            if (err) {
                toastr.error('Fehler')
            }
            if (res) {
                commit('SET_AUTOCPMPLETE_USERS', res.data)
            }
        },
        setSelectedUser ({ commit }, user:string) : void {
            commit('SET_SELECTED_USER', user)
        },
        resetSelectedUser ({ commit }) : void {
            commit('RESET_SELECTED_USER')
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
