import Vue from 'vue'
import VueI18n, { LocaleMessageObject } from 'vue-i18n'
import Lang from 'core/module_system/scripts/kajona/Lang'
Vue.use(VueI18n)
let de = {}
const locale = KAJONA_LANGUAGE

async function getLanguages () {
    const de : LocaleMessageObject = {
        reportconfigurator: await Lang.fetchModule('reportconfigurator', 'de')
    }
    const en : LocaleMessageObject = {
        reportconfigurator: await Lang.fetchModule('reportconfigurator', 'en')
    }

    return new VueI18n({
        locale,
        messages: { de, en }
    })
}

export default getLanguages()
