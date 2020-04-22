<template>
    <div class="row no-gutters">
        <div class="col">
            <div class="row no-gutters">
                <div class="col-12">
                    <h4 class="column-title">Timeline</h4>
                    <button-new-tweet :user="user" class="btn-new-tweet"></button-new-tweet>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="new-tweet-modal" tabindex="-1" role="dialog"
                 aria-labelledby="new-tweet-modalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="new-tweet-modalLabel">Nuevo Tweet</h5>
                            <button type="button" id="close-new-tweet-modal" class="close" aria-label="Close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                <div class="col-2 user-profile-img-container">
                                    <h1>HOLA</h1>
                                </div>
                                <div class="col">
                                    <textarea class="js-autoresize" v-model="newTweetText" maxlength="280"
                                              placeholder="¿Qué está pasando?"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="sendTweet" type="button" class="btn btn-primary btn-round">Twittear</button>
                        </div>
                    </div>
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
    import {setResizeListeners} from "../../../../../helpers/auto-resize.js";

    export default {
        props: [
            'timeline',
            'user'
        ],
        data() {
            return {
                newTweetText: ""
            }
        },
        mounted() {
            setResizeListeners(this.$el, ".js-autoresize");
        },
        methods: {
            sendTweet() {
                axios.post('/ajax/tweets/new', {'text': this.newTweetText})
                    .then((response) => {
                        if (response.data.status == 'success') {
                            this.$toast.success(response.data.message);
                            this.newTweetText = "";
                        }
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>
    $textColor: #3E396B;

    .modal {
        .modal-header {
            border: none;

            .close {
                margin: 0 10px 0 0 !important;
                padding: 0 !important;
                line-height: initial;
            }
        }

        .modal-body {
            .user-profile-img-container {
                padding-right: 15px;

                .user-profile-img {
                    width: 100%;
                    border-radius: 50%;
                }
            }

            textarea {
                width: 100%;
                height: 100px;
                border: none;
                font-size: 15pt;
                resize: none;
                color: rgb(33, 37, 41);

                &::placeholder {
                    font-size: 15pt;
                    font-weight: 500;
                    color: rgba(0, 0, 0, 0.3);
                }
            }
        }

        .modal-footer {
            border: none;
        }
    }

    .btn-new-tweet {
        position: absolute;
        top: 0;
        right: 0;

        margin-top: 0.2em;
        margin-right: 0.5em;
    }

    .column-title {
        text-align: center;
    }

    .tweet-list-row {
        .tweet-list-container {
            height: calc(100vh - 39.82px - 15px * 2 - 4px);
            overflow-x: hidden;
            overflow-y: scroll !important;

            border-left: 1px solid rgba(0, 0, 0, 0.125);
            border-top: 1px solid rgba(0, 0, 0, 0.125);
            border-top-left-radius: 5px;
        }
    }

    .container-fluid {
        padding: 0;
    }
</style>
