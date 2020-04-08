<template>
    <div class="row no-gutters">
        <div class="col">
            <div class="row no-gutters">
                <div class="col-12">
                    <h4 class="column-title">Timeline</h4>
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col tl-container">
                    <tweet v-for="(tweet, i) in timeline" :tweet="tweet" :key="tweet.id"></tweet>
                </div>
            </div>
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
            let columnTitleHeight = $('.column-title').height();

            $('.tl-container').height(dashHeight - columnTitleHeight - 15);
        }
    }
</script>

<style lang="scss" scoped>
    .column-title {
        text-align: center;
    }

    .row {
        .tl-container {
            overflow-x: hidden;
            overflow-y: scroll!important;
        }
    }

    .container-fluid {
        padding: 0;
    }
</style>
