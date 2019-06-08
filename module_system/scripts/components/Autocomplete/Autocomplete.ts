import { Component, Vue, Watch, Prop } from 'vue-property-decorator'
import uuid from 'uuid/v1'
import axios from 'axios'
@Component class Autocomplete extends Vue {
    @Prop({ type: String, required: true }) module : string // name of the module
    @Prop({ type: String, required: true }) action : string // name of the action
    @Prop({ type: String, required: true }) queryPropertyName : string // the name of property which stores the value of the userInput
    @Prop({ type: String, required: true }) extraProperties : string // extra post properties
    @Prop({ type: String, required: true }) jsonKey : string // name of key : needed to map the correct key in the autocomplete menu
    @Prop({ type: String, required: true }) label : string
    private userQuery : string = ''
    private results : Array<object> =[]
    private mappedResults : Array<string> = []
    private listId : string = uuid()
    private inputId : string = uuid()
    @Watch('userQuery') async onChange () : Promise<void> {
        if (this.userQuery !== '') {
            var found = false
            this.results.map(el => {
                if (el[this.jsonKey] === this.userQuery) {
                    found = true
                    this.$emit('select', el)
                }
            })
            if (!found) {
                const res = await axios.post(KAJONA_WEBPATH + '/xml.php?module=' + this.module + '&action=' + this.action + '&' + this.queryPropertyName + '=' + this.userQuery + this.extraProperties)
                this.results = res.data
                // this.mappedResults = res.data.map(el => {
                //     return el[this.jsonKey]
                // })
            }
        }
    }
    private deleteUserQuery () : void {
        this.userQuery = ''
        this.$emit('delete')
    }
}
export default Autocomplete
