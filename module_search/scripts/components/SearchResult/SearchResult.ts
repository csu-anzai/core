import { Component, Vue } from 'vue-property-decorator'
import { namespace } from 'vuex-class'
@Component class SearchResult extends Vue {
@namespace('SearchModule').State searchResults : Array<any>
}
export default SearchResult
