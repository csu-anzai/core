import Vue from 'vue'
import Router from 'vue-router'
import Reportgenerator from 'core_agp/module_reportconfigurator/scripts/components/Reportgenerator/Reportgenerator.vue'
Vue.use(<any>Router)

const router = new Router({
    routes: [
        { path: '/vm/reportconfigurator/:reportId',
            component: Reportgenerator,
            beforeEnter: resetContainer
        }
    ]
})
router.beforeEach((to, from, next) => {
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
})
function resetContainer (to, from, next) : void {
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
}

export default router
