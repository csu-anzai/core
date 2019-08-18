<template >
  <div v-if="langFetched">
    <form class="navbar-search pull-left" v-if="!dialogIsOpen">
      <div class="input-group">
        <input
          class="form-control search-query"
          @mousedown="open"
          :placeholder="$t('dashboard.globalSearchPlaceholder')"
        />
        <span class="input-group-addon">
          <i class="fa fa-search" aria-hidden="true"></i>
        </span>
      </div>
    </form>
    <Modal :show="dialogIsOpen" @close="close" @open="onModalOpen">
      <div class="modal-body">
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
            />
            <span class="input-group-addon" id="searchbarFilterToggle" @click="toggleFilter">
              <i
                class="fa fa-caret-down"
                v-bind:class="{'fa fa-caret-up' : !filterIsOpen , 'fa fa-caret-up' : filterIsOpen}"
              ></i>
            </span>
          </div>
        </form>
        <SearchbarFilter v-if="dialogIsOpen && filterIsOpen"></SearchbarFilter>
        <Loader :loading="isLoading"></Loader>
        <div v-if="showResultsNumber">
          <p>
            {{ $t("search.hitlist_text1") }}
            "{{ searchQuery }}"
            {{ $t("search.hitlist_text2") }}
            {{ searchResults.length }}
            {{ $t("search.hitlist_text3") }}
          </p>
        </div>
        <div id="searchResultsContainer">
          <div
            v-if="
              searchResults.length !== 0 &&
                dialogIsOpen &&
                userInput.length >= 2
            "
          >
            <SearchResult></SearchResult>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>
<script lang="ts" src="./Searchbar.ts"></script>
