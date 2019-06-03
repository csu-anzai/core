<template>
    <div>
        <p @click="toggleFilter">
            {{$t("search.form_additionalheader")}} 
            <i class="fa fa-caret-up" v-if="filterIsOpen"></i>
            <i class="fa fa-caret-down" v-else></i> 

        </p>
        <!-- <div class="btn-group buttonbar col-sm-12 form-group core-component-formentry-buttonbar" v-if="filterModules!==null" >
               <label class="btn btn-primary active" v-for="(module, index) in filterModules" :key="index">
                   <input type="checkbox" :value="module.id">
                   {{module.module}}
               </label>

        </div> -->
        <div v-if="filterIsOpen">
            <Loader v-if="filterModules===null" :loading="true"></Loader>
            <div v-else class="form-group core-component-formentry-buttonbar">
                <div class="btn-group buttonbar">
                    <label class="btn btn-primary active" v-for="(module, index) in filterModules" :key="index">
                   <input type="checkbox" :value="module.id" checked="checked">
                   {{module.module}}
               </label>
                </div>
            </div>
        </div>

    </div>
</template>
<script lang="ts" src="./SearchbarFilter.ts">
</script>
<style lang="less" scoped src="./SearchbarFilter.less">
</style>


