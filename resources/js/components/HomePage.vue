<template>
    <div class="container">
        <div class="row tl-container">
            <div class="col">
                <div v-for="(tweet, i) in tl" class="row tweet-wrapper">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header tw-username">@{{ tweet.retweeted_status ? tweet.retweeted_status.user.screen_name : tweet.user.screen_name }}</div>

                            <div class="card-body">
                                {{ tweet.text }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Timeline JSON</div>
                    <div class="card-body">
                        <pre>{{ tl }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'tl'
        ],
        created() {

        },
        data() {
            return {

            }
        },
        methods: {

        }
    }
</script>

<style lang="scss" scoped>
    .tw-username {
        font-weight: bold;
    }

    .tweet-wrapper:not(:first-child) {
        margin-top: 15px;
    }
</style>
