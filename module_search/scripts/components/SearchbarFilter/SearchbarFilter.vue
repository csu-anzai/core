<template>
  <form class="form-horizontal">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12" id="toggleFilters">
        <p @click="toggleFilter" class="btn btn-default">
          {{$t("search.form_additionalheader")}}
          <i class="fa fa-caret-up" v-if="filterIsOpen"></i>
          <i class="fa fa-caret-down" v-else></i>
        </p>
      </div>
    </div>
    <div v-if="filterIsOpen">
      <Loader v-if="filterModules===null" :loading="true"></Loader>
      <div v-else>
        <Multiselect
          :options="moduleNames"
          :label="$t('search.search_modules')"
          @select="onModulesChange"
        ></Multiselect>
        <Autocomplete
          @select="onUserSelect"
          @delete="onUserDelete"
          :label="$t('search.search_users')"
          @input="onAutocompleteInput"
          :jsonKey="'title'"
          :data="autoCompleteUsers"
        ></Autocomplete>
        <Datepicker
          v-on:change="onStartDateChange"
          :label="$t('search.form_search_changestartdate')"
          :format="datepickerFormat"
        ></Datepicker>
        <Datepicker
          v-on:change="onEndDateChange"
          :label="$t('search.form_search_changeenddate')"
          :format="datepickerFormat"
        ></Datepicker>
      </div>
    </div>
  </form>
</template>
<script lang="ts" src="./SearchbarFilter.ts">
</script>
