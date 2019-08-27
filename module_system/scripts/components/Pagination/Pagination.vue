<template>
  <div v-if="langFetched" class="pager">
    <ul class="pagination">
      <li v-if="current!==1" @click="previous()">
        <a>« {{$t('commons.commons_back')}}</a>
      </li>
      <li
        v-for="(item, index) in total"
        :key="index"
        v-bind:class="{active : current === index+1}"
        @click="changePage(index+1)"
      >
        <a v-bind:class="{active : current === index+1}">{{index+1}}</a>
      </li>
      <li @click="next()" v-if="current!==total">
        <a>{{$t('commons.commons_continue')}} »</a>
      </li>
      <li>
        <a>
          {{$t("system.pageview_total")}}
          {{total}}
        </a>
      </li>
    </ul>
  </div>
</template>

<script lang="ts" src="./Pagination.ts">
</script>
