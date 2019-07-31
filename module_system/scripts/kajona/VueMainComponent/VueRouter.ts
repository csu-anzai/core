import Vue from 'vue'
import Router from 'vue-router'
import Reportgenerator from 'core_agp/module_reportconfigurator/scripts/components/Reportgenerator/Reportgenerator.vue'
import ContentToolbar from 'core/module_system/scripts/kajona/ContentToolbar'
import BreadCrumb from 'core/module_system/scripts/kajona/Breadcrumb'
Vue.use(<any>Router)

const router = new Router({
    routes: [
        { path: '/vm/reportconfigurator/:reportId',
            component: Reportgenerator,
            beforeEnter: resetContainer
        }
    ]
})
function resetContainer (to, from, next) : void {
    ContentToolbar.resetBar()
    BreadCrumb.resetBar()
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
}

export default router
