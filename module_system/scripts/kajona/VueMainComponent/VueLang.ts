import Vue from 'vue'
import VueI18n from 'vue-i18n'
import Lang from 'core/module_system/scripts/kajona/Lang'
Vue.use(VueI18n)
let de = {}
const locale = KAJONA_LANGUAGE

async function getLanguages () {
    const de = {
        reportconfigurator: await Lang.fetchModule('reportconfigurator')
    }
    const en = {
        reportconfigurator: await Lang.fetchModule('reportconfigurator')
    }

    console.log(de)
    return new VueI18n({
        locale,
        messages: { de, en }
    })
}

export default getLanguages()
