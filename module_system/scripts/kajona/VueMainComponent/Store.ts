import Vue from 'vue'
import Vuex from 'vuex'
import RatingDetailModule from 'core_customer/module_hsbcact/scripts/modules/RatingDetailModule'

Vue.use(<any>Vuex)

export default new Vuex.Store({
    modules: {
        ratingModule: RatingDetailModule
    }
})
