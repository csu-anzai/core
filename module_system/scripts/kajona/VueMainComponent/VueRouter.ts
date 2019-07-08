import Vue from 'vue'
import Router from 'vue-router'
import FailCode from 'core_customer/module_hsbcact/scripts/components/FailCodeWrapper/FailCodeWrapper.vue'
Vue.use(<any>Router)

export default new Router({
    // mode:'history',
    routes: [
        {
            path:'/vm/FailCodeTest',
            component:FailCode
        }
    ]
})


