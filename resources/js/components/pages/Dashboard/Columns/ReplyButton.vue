<template>
    <div class="new-tweet-button-container">
        <i class="fa fa-comment" data-toggle="modal" :data-target="'#new-reply-modal' + tweet.id"></i>

        <!-- Modal -->
        <div @dragstart="disableDrag" class="modal fade" :id="'new-reply-modal' + tweet.id" tabindex="-1" role="dialog" :aria-labelledby="'label-new-reply-modal' + tweet.id" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" :id="'label-new-reply-modal' + tweet.id">Respondiendo a @{{ !tweet.retweeted_status ? tweet.user.screen_name : tweet.retweeted_status.user.screen_name }}</h5>
                        <button type="button" :id="'close-new-reply-modal' + tweet.id" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row no-gutters">
                            <div class="col-2 tweet-user-avatar-container">
                                <img :src="!tweet.retweeted_status ? tweet.user.profile_image_url : tweet.retweeted_status.user.profile_image_url" class="tweet-user-avatar" :alt="'Imagen de perfil de @' + tweet.user.screen_name">
                            </div>
                            <div class="col-10 tweet-content-container">
                                <div class="tweet-text" v-html="formatted_tweet"></div>
                            </div>
                        </div>
                        <div class="row no-gutters reply-container">
                            <div class="col-2 user-profile-img-container">
                                <img :src="user_avatar" class="user-profile-img" alt="Tu imagen de perfil">
                            </div>
                            <div class="col-10 tweet-content-container">
                                <textarea class="js-autoresize" v-model="replyText" maxlength="280" :placeholder="'Respondiendo a @' + !tweet.retweeted_status ? tweet.user.screen_name : tweet.retweeted_status.user.screen_name"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="sendReply" type="button" class="btn btn-primary btn-round">Enviar respuesta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {setResizeListeners} from "../../../../helpers/auto-resize.js";
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        props: [
            'tweet',
            'formatted_tweet',
            'user_avatar'
        ],
        mounted() {
            setResizeListeners(this.$el, ".js-autoresize");
        },
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;
    $tweetColor: #212529;

    .tweet-user-avatar-container {
        padding-right: 7.5px;

        .tweet-user-avatar {
            width: 100%;
            border-radius: 50%;
        }
    }

    .tweet-content-container {
        padding-left: 7.5px;

        .tweet-text {
            font-size: 14pt!important;
            font-weight: normal;
            color: $tweetColor;
        }
    }

    .reply-container {
        padding-top: 2em;
        margin-top: 2em;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .modal {
        .modal-header {
            border: none;

            i {
                padding: 9px;
                margin-left: -7px;
                cursor: pointer;
            }

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
                padding-right: 7.5px;

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
            display: block;
            text-align: right;

            border: none;

            .btn-info {
                color: #444;
                background-color: #e8e8e8;
                border: 0;

                &:hover {
                    background-color: darken(#e8e8e8, 7%);
                }
            }

            .scheduled-date {
                display: block!important;
                padding-right: 5px;

                font-weight: 500;
                text-align: right;
                color: $textColor;
            }
        }
    }
</style>
