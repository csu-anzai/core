interface DbTables {
  headline: string
  tables: Array<string>
}

interface TableData {
  columns: Array<Column>
  indexes: Array<Index>
  keys: Array<string>
}

interface Column {
  dbtype: string
  name: string
  nullable: string
  type: string
}

interface Index {
  description: string
  name: String
}

export { DbTables, TableData, Column, Index }
