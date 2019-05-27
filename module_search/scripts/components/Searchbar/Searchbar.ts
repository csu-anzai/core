import { Component, Vue } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import SearchResult from '../SearchResult/SearchResult.vue'
@Component({ components: { SearchResult } }) class Searchbar extends Vue {
    private userInput : String =''
     @namespace('SearchModule').Action triggerSearch: any
     @namespace('SearchModule').State searchResults : Array<any>
     private mounted () : void {
         var parent = document.getElementById('searchbarContainer')
         parent.appendChild(this.$el)
     }
     private onSubmit (e : Event) : void {
         e.preventDefault()
         console.log('submit : ', this.userInput)
     }
     private onInput (e:Event) : void {
         this.triggerSearch(this.userInput)
     }
}

export default Searchbar
