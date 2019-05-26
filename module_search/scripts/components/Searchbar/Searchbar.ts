import { Component, Vue } from 'vue-property-decorator'
import axios from 'axios'
@Component class Searchbar extends Vue {
    private userInput : String =''
    private mounted () : void {
        var parent = document.getElementById('searchbarContainer')
        parent.appendChild(this.$el)
    }
    private onSubmit (e : Event) : void {
        e.preventDefault()
        console.log('submit : ', this.userInput)
    }
    private onInput (e:Event) {
        console.log('changed value')
    }
}

export default Searchbar
