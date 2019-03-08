import { Vue, Component } from 'vue-property-decorator'
import axios from 'axios'
import to from 'await-to-js'
import { Index, TableData, DbTables } from './Interfaces'
import Col from '../Grid/Col.vue'
import Row from '../Grid/Row.vue'
import Table from '../Table/Table.vue'
const toastr = require('toastr')
@Component({
  components: {
  Col,
  Row,
  Table
  }
  })
class DbBrowser extends Vue {
  private dbTables: DbTables = {} as DbTables
  private selectedTable: string = ''
  private tableData: TableData = {} as TableData

  // get tables List fron the Api when the Component did mount
  private async mounted (): Promise<void> {
    const [err, res]: any = await to(
      axios.post(
        process.env.VUE_APP_BASE_URL +
          '/xml.php?admin=1&module=dbbrowser&action=apiListTables'
      )
    )
    if (err) {
      toastr.error('Fehler')
    }
    if (res) {
      this.dbTables = res.data
    }
  }

  private async getSelectedTable (): Promise<void> {
    const [err, res] = await to(
      axios.post(
        process.env.VUE_APP_BASE_URL +
          '/xml.php?admin=1&module=dbbrowser&action=apiSystemSchemaJson&table=' +
          this.selectedTable
      )
    )
    if (err) {
      toastr.error('Fehler')
    }
    if (res) {
      return (this.tableData = res.data)
    }
  }
  // set the selected table and get the data from the Api
  private setSelectedTable (table: string): void {
    this.selectedTable = table
    this.getSelectedTable()
  }

  private async addIndex (column: string): Promise<void> {
    const [err, res]: any = await to(
      axios.post(
        process.env.VUE_APP_BASE_URL +
          '/xml.php?admin=1&module=dbbrowser&action=apiAddIndex&table=' +
          this.selectedTable +
          '&column=' +
          column
      )
    )
    if (err) {
      toastr.error('Fehler')
    }
    if (res.data.status === true) {
      toastr.success('Hinzugefügt')
      return this.getSelectedTable()
    }
  }
  // delete the selected dbIndex
  private async deleteIndex (index: Index): Promise<void> {
    const [err, res] = await to(
      axios.post(
        process.env.VUE_APP_BASE_URL +
          '/xml.php?admin=1&module=dbbrowser&action=apiDeleteIndex&index=' +
          index.name +
          '&table=' +
          this.selectedTable
      )
    )
    if (err) {
      toastr.error('Fehler')
    }
    if (res.data.status === true) {
      toastr.success('Gelöscht')
      return this.getSelectedTable()
    }
  }

  // recreate index
  private async recreateIndex (index: Index): Promise<void> {
    const [err, res] = await to(
      axios.post(
        process.env.VUE_APP_BASE_URL +
          '/xml.php?admin=1&module=dbbrowser&action=apiRecreateIndex&index=' +
          index.name +
          '&table=' +
          this.selectedTable
      )
    )
    if (err) {
      toastr.error('Fehler')
    }
    if (res.data.status === true) {
      toastr.success('Index wurde neu erzeugt')
    }
  }
  // checks if the key is in indexes
  private isIndex (key: string): boolean {
    var found = false
    this.tableData.indexes.map(index => {
      if (index.description === key) {
        return (found = true)
      } else {
        return null
      }
    })
    return found
  }
}

export default DbBrowser
