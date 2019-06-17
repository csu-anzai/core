<template>
  <div class="row">
    <div class="form-group has-feedback">
      <label :for="id" class="col-sm-3 control-label">{{label}}</label>
      <div class="col-sm-6 ">
        <MSelect
          :id="id"
          :multiple="true"
          :options="options"
          v-model="selectedOptions"
          :searchable="true"
          :close-on-select="false"
          :show-labels="false"
        ></MSelect>
      </div>
      <b-col sm="2" class="form-opener">
        <span class="listButton">
          <a @click="deleteSelection">
            <i class="kj-icon fa fa-trash-o"></i>
          </a>
        </span>
      </b-col>
    </div>
  </div>
</template>
<script lang="ts" src="./Multiselect.ts">
</script>
<style lang="less" scoped src="./Multiselect.less">
</style>

