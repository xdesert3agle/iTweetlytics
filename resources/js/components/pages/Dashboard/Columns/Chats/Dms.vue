<template>
    <div class="row no-gutters chat-container">
        <div class="col">
            <div class="row no-gutters">
                <div class="col-12">
                    <h4 class="column-title">Mensajes</h4>
                </div>
            </div>

            <div class="row no-gutters">
                <div v-if="!clickedChat" class="col chat-list-container">
                    <div v-for="(chat, i) in chats" @click="clickedChat = chat" class="row chat-preview-container">
                        <div class="col">
                            <div class="card chat-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-2">
                                            <img class="chat-user-avatar" :src="chat.user.profile_image_url" :alt="'Imagen de perfil de @' + chat.user.screen_name">
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
                <chat v-else @back="goBack" class="col-12 animated slideInRight fastest" :chat="clickedChat" :userId="user.twitter_profiles[0].id"></chat>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'chats',
            'user'
        ],
        data() {
            return {
                clickedChat: null
            }
        },
        methods: {
            goBack() {
                this.clickedChat = null;
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;

    .fastest {
        -webkit-animation-duration: 200ms;
        animation-duration: 200ms;
    }

    .column-title {
        text-align: center;
    }

    .chat-container {
        .chat-list-container {
            height: calc(100vh - 39.82px - 15px * 2 - 72.17px - 15px * 2 - 4px);
            overflow-x: hidden;
            overflow-y: scroll!important;

            .chat-preview-container {
                cursor: pointer;

                .card {
                    border-radius: 0;

                    .card-body {
                        padding: 15px;

                        .chat-user-avatar {
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

                        .chat-options {
                            margin-top: 10px;

                            .chat-action {
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

                &:not(:first-child) {
                    .chat-card {
                        border-top: none;
                    }
                }
            }
        }

        &:not(:first-child) {
            .chat-preview-container {
                border-top: none;
            }
        }
    }
</style>
