import { Vue, Component, Watch } from 'vue-property-decorator'
import Searchbar from 'core/module_search/scripts/components/Searchbar/Searchbar.vue'
import BootstrapVue from 'bootstrap-vue'

import 'bootstrap-vue/dist/bootstrap-vue.css'
Vue.use(BootstrapVue)
@Component({ components: { Searchbar } })
class VueMain extends Vue {
    mounted () {
    // register i18n globally to change the languages from outside Vue
        (<any>window).i18n = this.$i18n
    }
}
export default VueMain
