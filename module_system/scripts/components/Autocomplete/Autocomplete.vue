<template>
 <b-row>
    <div class="form-group has-feedback">
    <label :for="inputId" class="col-sm-3 control-label">{{label}}</label>
    <b-col sm="6" md="6" lg="6">
      <input type="text" :id="inputId" class="form-control" v-model="input">

      <span class="form-control-feedback loading-feedback">
        <i class="fa fa-keyboard-o"></i>
      </span>
      <div :id="listId"></div>
    </b-col>
    <b-col sm="2" md="2" lg="2" class="form-opener">
      <span class="listButton">
        <a @click="deleteInput">
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
