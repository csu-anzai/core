import Vue from 'vue'
import VueI18n from 'vue-i18n'
import SearchDe from 'core/module_search/scripts/Lang/lang_search_de'
import SearchEn from 'core/module_search/scripts/Lang/lang_search_en'
import DashboardDe from 'core/module_dashboard/scripts/Lang/lang_dashboard_de'
import DashboardEn from 'core/module_dashboard/scripts/Lang/lang_dashboard_en'
import SystemDe from 'core/module_system/scripts/Lang/lang_system_de'
import SystemEn from 'core/module_system/scripts/Lang/lang_system_en'
Vue.use(VueI18n)
const locale = KAJONA_LANGUAGE
const messages = {
    de: {
        search: SearchDe,
        dashboard: DashboardDe,
        system: SystemDe
    },
    en: {
        search: SearchEn,
        dashboard: DashboardEn,
        system: SystemEn
    }
}
export default new VueI18n({
    locale,
    messages
})
