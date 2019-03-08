<template>
  <div v-if="Object.keys(dbTables).length !== 0">
    <div v-if="'tables' in dbTables">
      <Row>
        <Col id="dbTablesContainer" :sm="12" :md="3" :lg="3" :xl="3">
          <div
            v-for="(table, index) in dbTables.tables"
            :key="index"
            class="dbTable"
            @click="setSelectedTable(table)"
          >
            <p>{{table}}</p>
          </div>
        </Col>

        <Col :sm="12" :md="9" :lg="9" :xl="9">
          <h1 v-if="selectedTable===''">Wählen Sie eine Tablelle aus</h1>
          <h1 v-else>Tabelle : {{selectedTable}}</h1>
          <div v-if="Object.keys(tableData).length !== 0">
            <h1>Spalten</h1>
            <!-- <Table :head="['Name','DatenTyp' ,'Datentyp DB' , 'Null']" :body="tableData.columns"></Table> -->
            <table class="dbBrowserTable table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Datentyp intern</th>
                  <th>Datentyp DB</th>
                  <th>Null</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(column, index) in tableData.columns" :key="index">
                  <td>
                    <i class="fa fa-columns" aria-hidden="true"></i>
                    {{column.name}}
                    <i
                      v-if="column.name === tableData.keys[0]"
                      class="fa fa-key"
                      aria-hidden="true"
                    ></i>
                  </td>
                  <td>{{column.type}}</td>
                  <td>{{column.dbtype}}</td>
                  <td>{{column.nullable}}</td>
                  <td>
                    <i
                      class="fa fa-bolt"
                      aria-hidden="true"
                      v-if="!isIndex(column.name)"
                      @click="addIndex(column.name)"
                    ></i>
                  </td>
                </tr>
              </tbody>
            </table>
            <h1>Primary Keys</h1>
            <table class="dbBrowserTable table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(primaryKey, index) in tableData.keys" :key="index">
                  <td>{{primaryKey}}</td>
                </tr>
              </tbody>
            </table>
            <h1>Indexes</h1>
            <table class="dbBrowserTable table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Beschreibung</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(dbIndex, index) in tableData.indexes" :key="index">
                  <td>{{dbIndex.name}}</td>
                  <td>{{dbIndex.description}}</td>
                  <td>
                    <i class="fa fa-trash-o" aria-hidden="true" @click="deleteIndex(dbIndex)"></i>
                    <i class="fa fa-refresh" aria-hidden="true" @click="recreateIndex(dbIndex)"></i>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </Col>
      </Row>
    </div>
  </div>
</template>

<script lang="ts" src="./DbBrowserComponent.ts">
</script>
<style lang="scss" scoped src="./DbBrowserComponent.scss">
</style>

