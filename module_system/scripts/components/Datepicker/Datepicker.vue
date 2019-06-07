<template>
  <b-row>
    <b-col sm="3" md="3" lg="3">{{label}}</b-col>
    <b-col sm="9" md="9" lg="9">
      <div class="input-group">
        <div class="input-group-addon">
          <i class="fa fa-calendar-o"></i>
        </div>
        <input type="text" :id="id" class="form-control">
      </div>
    </b-col>
  </b-row>
</template>
<script lang="ts" src="./Datepicker.ts">
</script>
<style lang="less" scoped src="./Datepicker.less">
</style>


