<template>
    <div>
        <div v-for="(item, index) in searchResults" :key="index">
        <p>
            {{item.description}}
        </p>
    </div>
    </div>
</template>

<script lang="ts" src="./SearchResult">
</script>
<style lang="less" scoped src="./SearchResult.less">

</style>
