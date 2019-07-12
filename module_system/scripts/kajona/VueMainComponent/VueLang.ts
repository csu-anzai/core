import Vue from 'vue'
import VueI18n from 'vue-i18n'
Vue.use(VueI18n)
const locale = KAJONA_LANGUAGE
const messages = {
    de: {
    },
    en: {
    }
}
export default new VueI18n({
    locale,
    messages
})
