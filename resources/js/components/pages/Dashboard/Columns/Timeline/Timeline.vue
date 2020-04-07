<template>
    <div class="row">
        <div class="col tl-container">
            <tweet v-for="(tweet, i) in timeline" :tweet="tweet" :key="tweet.id"></tweet>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'timeline'
        ],
        mounted() {
            let dashHeight = $('.dash-container').height();
            $('.tl-container').height(dashHeight - 50);
        }
    }
</script>

<style lang="scss" scoped>
    .row {
        .tl-container {
            overflow-y: scroll!important;
        }
    }

    .container-fluid {
        padding: 0;
    }
</style>
