import { Component, Vue } from 'vue-property-decorator'
import axios from 'axios'
@Component class Test extends Vue {
    mounted () {
        console.log('mounted')     
}
}
export default Test
