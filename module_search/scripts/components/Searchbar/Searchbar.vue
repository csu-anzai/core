<template>
<div>
  <div v-bind:class="{ searchBarOuterContainerDialog: dialogIsOpen }"  @mousedown="close"></div>
     <div v-bind:class="{ searchBarInnerContainerDialog: dialogIsOpen }" >
      <form @submit="onSubmit" class="navbar-search pull-left">
      <div class="input-group">
          <input type="text" name="search_query" class="form-control search-query" @input="onInput" v-model="userInput" @mousedown="open"  >
      <span class="input-group-addon">
        <i class="fa fa-search" aria-hidden="true"></i>
      </span>
      </div>
    </form>
    <div v-if="searchResults.length!==0" id="searchResultsContainer">
      <SearchResult></SearchResult>
    </div>
    </div>
</div>
</template>
<script lang="ts" src="./Searchbar.ts">
</script>
<style lang="less" scoped src="./Searchbar.less">
</style>
