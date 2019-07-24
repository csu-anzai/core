import Vue from 'vue'
import Router from 'vue-router'
import FailCode from 'core_customer/module_hsbcact/scripts/components/FailCodeWrapper/FailCodeWrapper.vue'
import store from './Store'
Vue.use(<any>Router)

export default new Router({
    // mode:'history',
    routes: [
        {
            path: '/vm/FailCodeTest',
            component: FailCode,
            props: route => ({
                query: {
                    objects: route.query.objects
                }
            }),
            beforeEnter: (to, from, next) => {
                let objects = to.query.objects.toString()
                let str =objects.split(',')
                store.commit('failCode/SET_OBJECTS', str)
                next()
            }
        }
    ]
})
