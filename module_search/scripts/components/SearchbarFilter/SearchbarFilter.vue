<template>
  <div>
    <b-row>
      <b-col sm="12" md="12" lg="12" id="toggleFilters">
        <p @click="toggleFilter" class="btn btn-default">
          {{$t("search.form_additionalheader")}}
          <i class="fa fa-caret-up" v-if="filterIsOpen"></i>
          <i class="fa fa-caret-down" v-else></i>
        </p>
      </b-col>
    </b-row>
    <div v-if="filterIsOpen">
      <Loader v-if="filterModules===null" :loading="true"></Loader>
      <div v-else>
        <b-row>
          <b-col sm="3" md="3" lg="3">{{$t("search.search_modules")}}</b-col>
          <b-col sm="9" md="9" lg="9">
            <Multiselect
              :multiple="true"
              :options="moduleNames"
              v-model="selectedModules"
              :searchable="true"
              :close-on-select="false"
              :show-labels="false"
            ></Multiselect>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="3" md="3" lg="3">{{$t("search.search_users")}}</b-col>
          <b-col sm="6" md="6" lg="6">
            <b-form-input list="userFilter" v-model="userQuery" autocomplete="off"></b-form-input>
            <datalist id="userFilter">
              <option v-for="(user, index) in users" :key="index">{{ user }}</option>
            </datalist>
          </b-col>
          <b-col sm="3" md="3" lg="3">
            <span class="listButton">
              <a>
                <i class="kj-icon fa fa-search"></i>
              </a>
            </span>
            <span class="listButton">
              <a>
                <i class="kj-icon fa fa-trash-o"></i>
              </a>
            </span>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="3" md="3" lg="3">{{$t("search.form_search_changestartdate")}}</b-col>
          <b-col sm="9" md="9" lg="9">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-calendar-o"></i>
              </div>
              <datePicker v-model="date" :config="dateOptions"></datePicker>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col sm="3" md="3" lg="3">{{$t("search.form_search_changeenddate")}}</b-col>
          <b-col sm="9" md="9" lg="9">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-calendar-o"></i>
              </div>
              <datePicker v-model="date" :config="dateOptions"></datePicker>
            </div>
          </b-col>
        </b-row>
      </div>
    </div>
  </div>
</template>
<script lang="ts" src="./SearchbarFilter.ts">
</script>
<style lang="less" scoped src="./SearchbarFilter.less">
</style>
