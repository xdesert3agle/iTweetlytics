<template>
    <div class="row no-gutters">
        <div class="col">
            <div class="row no-gutters">
                <div class="col-12">
                    <h4 class="column-title">Mensajes</h4>
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col tweet-list-container">
                    <div v-for="(chat, i) in chats" class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-2">
                                    <img class="tweet-user-avatar" :src="chat.user.profile_image_url" :alt="'Imagen de perfil de @' + chat.user.screen_name">
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <span class="name">{{ chat.user.name }}</span>
                                            <span class="screen-name text-muted">@{{ chat.user.screen_name }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <span class="chat-preview-message">{{ chat.messages[0].message_create.message_data.text }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'chats'
        ],
        mounted() {
            let dashHeight = $('.dash-container').height();
            let columnTitleHeight = $('.column-title').height();

            $('.tweet-list-container').height(dashHeight - columnTitleHeight - 15);
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;

    .tweet-container {
        .tweet-wrapper {
            .tweet-card {
                border-radius: 0;

                .card-body {
                    padding: 15px;

                    .tweet-user-avatar {
                        border-radius: 50%;
                    }

                    .name {
                        font-weight: bold;
                    }

                    .screen-name {
                        color: #a7a2ce;
                    }

                    .chat-preview-message {

                    }

                    .tweet-options {
                        margin-top: 10px;

                        .tweet-action {
                            cursor: pointer;
                            color: lighten(black, 65%);
                            transition: 200ms;
                            font-weight: bold;

                            &.action-comment {
                                &:hover {
                                    color: lighten($primaryColor, 10%);
                                }
                            }

                            &.action-retweet {
                                &.retweeted {
                                    color: lighten(green, 10%);
                                }

                                &:hover {
                                    color: lighten(green, 10%);
                                }
                            }

                            &.action-like {
                                &.liked {
                                    color: lighten(red, 10%);
                                }

                                &:hover {
                                    color: lighten(red, 10%);
                                }
                            }

                            &.action-share {
                                &:hover {
                                    color: lighten($primaryColor, 10%);
                                }
                            }
                        }

                        &.retweeted-tweet {
                            .action-retweet {
                                color: lighten(green, 10%);
                            }
                        }

                        &.favorited-tweet {
                            .action-like {
                                color: lighten(red, 10%);
                            }
                        }
                    }
                }
            }
        }

        &:not(:first-child) {
            .tweet-card {
                border-top: none;
            }
        }

        &:first-child {
            .tweet-card {
                border-top-left-radius: 5px;
            }
        }

        &:last-child {
            .tweet-card {
                border-bottom-left-radius: 5px;
            }
        }
    }
</style>
