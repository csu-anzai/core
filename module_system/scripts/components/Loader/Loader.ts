import { Component, Vue, Prop } from 'vue-property-decorator'
@Component class Loader extends Vue {
@Prop(Boolean) loading : boolean
}
export default Loader
