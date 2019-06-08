<template>
<b-row>
    <div class="form-group core-component-formentry-datesingle">
    <label :for="id" class="col-sm-3 control-label">{{label}}</label>
    <b-col sm="6" md="6" lg="6">
      <div class="input-group">
        <div class="input-group-addon">
          <i class="fa fa-calendar-o"></i>
        </div>
        <input type="text" :id="id" class="form-control">
      </div>
    </b-col>
  </div>
</b-row>
</template>
<script lang="ts" src="./Datepicker.ts">
</script>
<style lang="less" scoped src="./Datepicker.less">
</style>
