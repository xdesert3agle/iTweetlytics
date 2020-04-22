<template>
    <div class="new-tweet-button-container">
        <button type="button" class="btn btn-primary btn-small btn-icon btn-tweet" data-toggle="modal"
                data-target="#new-tweet-modal">
            <i class="fab fa-twitter"></i>
            <span>Tweet</span>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="new-tweet-modal" tabindex="-1" role="dialog"
             aria-labelledby="label-new-tweet-modal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="label-new-tweet-modal">Nuevo Tweet</h5>
                        <button type="button" id="close-new-tweet-modal" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row no-gutters">
                            <div class="col-2 user-profile-img-container">
                                <img :src="user.twitter_profiles.profile_image_url" class="user-profile-img"
                                     alt="Tu imagen de perfil">
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
    </div>
</template>

<script>
    import {setResizeListeners} from "../../../../../helpers/auto-resize.js";

    export default {
        props: [
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

            .label-new-tweet-modal {
                color: $textColor;
            }

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
</style>
