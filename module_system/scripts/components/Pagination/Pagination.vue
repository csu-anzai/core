<template>
  <div v-if="langFetched" class="pager">
    <BPagination
      :total-rows="total"
      per-page="1"
      :prev-text="$t('commons.commons_back')"
      :next-text="$t('commons.commons_continue')"
      v-model="currentPage"
      @change="onChange($event)"
    ></BPagination>
  </div>
</template>

<script lang="ts" src="./Pagination.ts">
</script>
<style lang="less" scoped src="./Pagination.less">
</style>
