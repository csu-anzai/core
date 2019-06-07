<template>
    <div>
         <b-form-input list="userFilter" v-model="userQuery" autocomplete="off"></b-form-input>
            <datalist id="userFilter">
              <option v-for="(element, index) in mappedResults" :key="index">{{ element }}</option>
            </datalist>
    </div>
</template>
<script lang="ts" src="./Autocomplete.ts">
</script>
<style lang="less" scoped src="./Autocomplete.less">
</style>

