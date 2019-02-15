import { Vue, Component, Prop } from 'vue-property-decorator'

@Component
class Col extends Vue {
  @Prop(Number) sm!: number
  @Prop(Number) md!: number
  @Prop(Number) lg!: number
  @Prop(Number) xl!: number

  private get getClassName (): string {
    var className =
      'col-sm-' +
      this.sm +
      ' ' +
      'col-md-' +
      this.md +
      ' ' +
      'col-lg-' +
      this.lg +
      ' ' +
      'col-xl-' +
      this.xl
    return className
  }
}

export default Col
