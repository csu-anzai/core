import Vue from 'vue'
import Vuex from 'vuex'
import ReportconfiguratorModule from 'core_agp/module_reportconfigurator/scripts/modules/ReportconfiguratorModule'
Vue.use(<any>Vuex)

export default new Vuex.Store({
    modules: {
        ReportconfiguratorModule: ReportconfiguratorModule
    }
})
