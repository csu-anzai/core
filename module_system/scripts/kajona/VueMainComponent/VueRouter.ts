import Vue from 'vue'
import Router from 'vue-router'
Vue.use(<any>Router)

const router = new Router({
    routes: []
})
function resetContainer (to, from, next) : void {
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
}

export default router
