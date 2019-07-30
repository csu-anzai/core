import Vue from 'vue'
import Router from 'vue-router'
Vue.use(<any>Router)

const router = new Router({
    routes: []
})
router.beforeEach((to, from, next) => {
    let moduleOutput = document.getElementById('moduleOutput')
    moduleOutput.innerHTML = ''
    next()
})

export default router
