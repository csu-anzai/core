<template>
  <div class="row">
    <div class="form-group core-component-formentry-datesingle">
      <label :for="id" class="col-sm-3 control-label">{{label}}</label>
      <div class="col-sm-6 col-md-6 col-lg-6">
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar-o"></i>
          </div>
          <input type="text" :id="id" class="form-control">
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" src="./Datepicker.ts">
</script>
<style lang="less" scoped src="./Datepicker.less">
</style>
