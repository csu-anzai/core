import Vue from 'vue'
import VueI18n, { LocaleMessageObject } from 'vue-i18n'
import Lang from 'core/module_system/scripts/kajona/Lang'
Vue.use(VueI18n)

async function getLanguages () {
    const de : LocaleMessageObject = {
        search: await Lang.fetchModule('search', 'de'),
        dashboard: await Lang.fetchModule('dashboard', 'de'),
        system: await Lang.fetchModule('system', 'de')
    }
    const en : LocaleMessageObject = {
        search: await Lang.fetchModule('search', 'en'),
        dashboard: await Lang.fetchModule('dashboard', 'en'),
        system: await Lang.fetchModule('system', 'en')
    }

    return new VueI18n({
        locale: KAJONA_LANGUAGE,
        messages: { de, en }
    })
}

export default getLanguages()
