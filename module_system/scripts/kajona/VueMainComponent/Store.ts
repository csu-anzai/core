import Vue from 'vue'
import Vuex from 'vuex'
import SearchModule from 'core/module_search/scripts/modules/SearchModule'

Vue.use(<any>Vuex)

export default new Vuex.Store({
    modules: { SearchModule: SearchModule }
})
