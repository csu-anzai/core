import Vue from 'vue'
import Router from 'vue-router'
import ContentToolbar from 'core/module_system/scripts/kajona/ContentToolbar'
import BreadCrumb from 'core/module_system/scripts/kajona/Breadcrumb'
Vue.use(<any>Router)

const router = new Router({
    routes: []
})
function resetContainer (to, from, next) : void {
    ContentToolbar.resetBar()
    BreadCrumb.resetBar()
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
}

export default router
