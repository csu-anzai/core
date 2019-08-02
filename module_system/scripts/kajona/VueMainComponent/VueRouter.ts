import Vue from 'vue'
import VueRouter from 'vue-router'
import Router from '../Router'
import Reportgenerator from 'core_agp/module_reportconfigurator/scripts/components/Reportgenerator/Reportgenerator.vue'
Vue.use(<any>VueRouter)

const router = new VueRouter({
    routes: [
        { path: '/vm/reportconfigurator/:reportId',
            component: Reportgenerator,
            beforeEnter: resetContainer
        }
    ]
})
function resetContainer (to, from, next) : void {
    Router.cleanPage(true)
    next()
}

export default router
