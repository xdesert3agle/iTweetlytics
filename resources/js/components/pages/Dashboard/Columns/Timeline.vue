<template>
    <div class="row no-gutters">
        <div class="col">
            <div class="row no-gutters column-title-row">
                <div class="col-12">
                    <h4 class="column-title">Timeline</h4>
                </div>
            </div>

            <div class="row no-gutters tweet-list-row">
                <div class="col tweet-list-container">
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
    }
</script>

<style lang="scss" scoped>
    .column-title {
        text-align: center;
    }

    .tweet-list-row {
        .tweet-list-container {
            height: calc(100vh - 39.82px - 15px * 2 - 72.17px - 15px * 2 - 4px);
            overflow-x: hidden;
            overflow-y: scroll!important;

            border-left: 1px solid rgba(0, 0, 0, 0.125);
            border-top: 1px solid rgba(0, 0, 0, 0.125);
            border-top-left-radius: 5px;
        }
    }

    .container-fluid {
        padding: 0;
    }
</style>
