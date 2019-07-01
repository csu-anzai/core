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
          :tooltip="$t('search.delete_action')"
        ></Multiselect>
        <Autocomplete
          @select="onUserSelect"
          @delete="onUserDelete"
          @input="onAutocompleteInput"
          :loading="fetchingUsers"
          :label="$t('search.search_users')"
          :data="parsedAutoCompleteData"
          :tooltip="$t('search.delete_action')"
        ></Autocomplete>
        <Datepicker
          v-on:change="onStartDateChange"
          :label="$t('search.form_search_changestartdate')"
          :format="datepickerFormat"
          :tooltip="$t('search.delete_action')"
        ></Datepicker>
        <Datepicker
          v-on:change="onEndDateChange"
          :label="$t('search.form_search_changeenddate')"
          :format="datepickerFormat"
          :tooltip="$t('search.delete_action')"
        ></Datepicker>
      </div>
    </div>
  </form>
</template>
<script lang="ts" src="./SearchbarFilter.ts">
</script>
