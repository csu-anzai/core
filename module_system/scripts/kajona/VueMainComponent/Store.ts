import Vue from 'vue'
import Vuex from 'vuex'
import failCodeModule from '@/core_customer/module_hsbcact/scripts/modules/FailCodeModule'

Vue.use(<any>Vuex)
export default new Vuex.Store({
    modules: {
        failCode: failCodeModule
    }
})
