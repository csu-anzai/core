import { Component, Vue, Watch } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
import SearchResult from '../SearchResult/SearchResult.vue'
import Loader from 'core/module_system/scripts/components/Loader.vue'
import SearchbarFilter from '../SearchbarFilter/SearchbarFilter.vue'
@Component({ components: { SearchResult, Loader, SearchbarFilter } }) class Searchbar extends Vue {
     @namespace('SearchModule').Action triggerSearch: any
     @namespace('SearchModule').Action resetSearchResults: any
     @namespace('SearchModule').State searchResults : Array<any>
     @namespace('SearchModule').State dialogIsOpen : boolean
     @namespace('SearchModule').State searchQuery : String
     @namespace('SearchModule').State fetchingResults : boolean
     @namespace('SearchModule').Action openDialog: any
     @namespace('SearchModule').Action closeDialog: any
     @namespace('SearchModule').Action resetSearchQuery: any
     @namespace('SearchModule').Action setSearchQuery : any

     private userInput : String =''
     private inputTimer : number
     private mounted () : void {
         var parent = document.getElementById('searchbarContainer')
         parent.appendChild(this.$el)
         // add event listener : ctrl + f opens searchbar , Esc closes searchbar
         document.body.addEventListener('keydown', this.shortcutHandler)
     }
     private destroyed () : void {
         document.body.removeEventListener('keydown', this.shortcutHandler)
     }
     private onSubmit (e : Event) : void {
         e.preventDefault()
     }
     private onInput (e: Event) : void {
         clearTimeout(this.inputTimer)
         this.setSearchQuery(this.userInput)
         this.inputTimer = window.setTimeout(() => {
             if (this.userInput.length > 1) {
                 this.triggerSearch()
             }
         }, 500)
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
      private shortcutHandler (e :KeyboardEvent) : void {
          if (e.ctrlKey && e.key === 'f' && !this.dialogIsOpen) {
              e.preventDefault()
              document.getElementById('searchbarInput').focus()
              this.open()
          }
          if (e.key === 'Escape' && this.dialogIsOpen) {
              this.close()
          }
      }
      private get dialogClassName () : string {
          if (!this.dialogIsOpen) {
              return ''
          } else {
              if (this.userInput === '') {
                  return 'searchbarContainerSemiOpen searchBarInnerContainer'
              } else {
                  return 'searchbarContainerOpen searchBarInnerContainer'
              }
          }
      }
}

export default Searchbar