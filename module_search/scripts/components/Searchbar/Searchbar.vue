<template>
  <div>
    <div v-bind:class="{ searchBarOuterContainer: dialogIsOpen }" @mousedown="close"></div>
    <div :class="dialogClassName">
      <form @submit="onSubmit" class="navbar-search pull-left">
        <div class="input-group">
          <input
            id="searchbarInput"
            type="text"
            name="search_query"
            class="form-control search-query"
            @input="onInput"
            v-model="userInput"
            @mousedown="open"
            autocomplete="off"
            :placeholder="$t('dashboard.globalSearchPlaceholder')"
          >
          <span class="input-group-addon">
            <i class="fa fa-search" aria-hidden="true"></i>
          </span>
        </div>
      </form>
      <div v-if="userInput!==''">
        <Loader :loading="loading"></Loader>
      </div>
      <div v-if="dialogIsOpen">
        <SearchbarFilter></SearchbarFilter>
      </div>
      <div id="searchResultsContainer">
        <div v-if="searchResults.length!==0 && userInput!==''">
          <SearchResult></SearchResult>
        </div>
        <!-- <div v-if="userInput!=='' && searchResults.length===0">
          <h1>Keine Ergebnisse</h1>
        </div> -->
      </div>
    </div>
  </div>
</template>
<script lang="ts" src="./Searchbar.ts">
</script>
<style lang="less" scoped src="./Searchbar.less">
</style>
