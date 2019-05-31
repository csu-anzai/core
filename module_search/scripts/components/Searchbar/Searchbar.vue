<template>
<div>
  <div v-bind:class="{ searchBarOuterContainerDialog: dialogIsOpen }"  @mousedown="close"></div>
     <div v-bind:class="{ searchBarInnerContainerDialog: dialogIsOpen }" >
      <form @submit="onSubmit" class="navbar-search pull-left">
      <div class="input-group">
          <input id="searchbarInput" type="text" name="search_query" class="form-control search-query" @input="onInput" v-model="userInput" @mousedown="open" autocomplete="off" :placeholder="$t('dashboard.globalSearchPlaceholder')">
      <span class="input-group-addon">
        <i class="fa fa-search" aria-hidden="true"></i>
      </span>
      </div>
    </form>
    <div class="loaderContainer">
      <Loader :loading="loading"></Loader>
    </div>
    <div  id="searchResultsContainer">
      <div v-if="searchResults.length!==0">
      <SearchResult></SearchResult>
    </div>
    </div>
    </div>
</div>
</template>
<script lang="ts" src="./Searchbar.ts">
</script>
<style lang="less" scoped src="./Searchbar.less">
</style>
