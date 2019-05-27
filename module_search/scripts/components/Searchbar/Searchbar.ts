import { Component, Vue, Watch } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import SearchResult from '../SearchResult/SearchResult.vue'
@Component({ components: { SearchResult } }) class Searchbar extends Vue {
     @namespace('SearchModule').Action triggerSearch: any
     @namespace('SearchModule').Action resetSearchResults: any
     @namespace('SearchModule').State searchResults : Array<any>
     @namespace('SearchModule').State dialogIsOpen : boolean
     @namespace('SearchModule').State searchQuery : String
     @namespace('SearchModule').Action openDialog: any
     @namespace('SearchModule').Action closeDialog: any
     @namespace('SearchModule').Action resetSearchQuery: any

     private userInput : String =''
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
     private open () : void {
         this.openDialog()
     }
     private close () : void {
         this.closeDialog()
         this.resetSearchQuery()
         this.resetSearchResults()
     }
     @Watch('searchQuery') onSearchQueryChange () {
         if (this.searchQuery === '') {
             this.userInput = ''
         }
     }
}

export default Searchbar
