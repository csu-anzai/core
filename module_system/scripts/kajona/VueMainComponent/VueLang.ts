import Vue from 'vue'
import VueI18n, { LocaleMessageObject } from 'vue-i18n'
import Lang from 'core/module_system/scripts/kajona/Lang'
Vue.use(VueI18n)

async function getLanguages () {
    const de : LocaleMessageObject = {

    }
    const en : LocaleMessageObject = {

    }

    return new VueI18n({
        locale: KAJONA_LANGUAGE,
        messages: { de, en }
    })
}

export default getLanguages()
