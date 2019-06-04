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
        <Loader :loading="fetchingResults"></Loader>
      </div>
      <div v-if="dialogIsOpen">
        <SearchbarFilter></SearchbarFilter>
      </div>
      <div v-if="dialogIsOpen && userInput!=='' && !fetchingResults">
        <p>
          {{$t("search.hitlist_text1")}}
          "{{searchQuery}}"
          {{$t("search.hitlist_text2")}}
          {{searchResults.length}}
          {{$t("search.hitlist_text3")}}
        </p>
      </div>
      <div id="searchResultsContainer">
        <div v-if="searchResults.length!==0 && userInput!=='' && !fetchingResults">
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
