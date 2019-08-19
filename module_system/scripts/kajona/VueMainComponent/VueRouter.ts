import Vue from 'vue'
import VueRouter from 'vue-router'
import Router from '../Router'
import RatingDetail from 'core_customer/module_hsbcact/scripts/components/RatingDetail.vue'
Vue.use(<any>VueRouter)

const router = new VueRouter({
    routes: [
        { path: '/vm/evaluation/:systemId/details',
            component: RatingDetail,
            beforeEnter: resetContainer
        }
    ]
})
function resetContainer (to, from, next) : void {
    Router.cleanPage(true)
    next()
}

export default router
