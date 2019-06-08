<template>
 <b-row>
    <div class="form-group has-feedback">
    <label :for="inputId" class="col-sm-3 control-label">{{label}}</label>
    <b-col sm="6" md="6" lg="6">
      <b-form-input :list="listId" v-model="userQuery" autocomplete="off" :id="inputId"></b-form-input>
      <datalist :id="listId">
        <option
          v-for="(element, index) in results"
          :key="index"
          v-html="element.icon + ' ' +element[jsonKey]"
        ></option>
      </datalist>
    </b-col>
    <b-col sm="2" md="2" lg="2" class="form-opener">
      <span class="listButton">
        <a @click="deleteUserQuery">
          <i class="kj-icon fa fa-trash-o"></i>
        </a>
      </span>
    </b-col>
  </div>
 </b-row>
</template>
<script lang="ts" src="./Autocomplete.ts">
</script>
<style lang="less" scoped src="./Autocomplete.less">
</style>
