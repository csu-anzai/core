import { Vue, Component, Prop } from "vue-property-decorator";

@Component
class Table extends Vue {
  @Prop(Array) head!: Array<string>;
  @Prop(Array) body!: Array<object>;
}

export default Table;
