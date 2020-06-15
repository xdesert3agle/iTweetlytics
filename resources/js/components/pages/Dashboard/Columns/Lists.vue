<template>
    <div class="row no-gutters lists-column-container">
        <div class="col">
            <div class="row no-gutters">
                <div class="col">
                    <div class="title-with-back-button">
                        <i v-if="!isChoosingList" @click="isChoosingList = true" class="fas fa-chevron-left"></i>
                        <h4 class="column-title">{{ isChoosingList ? "Listas" : lists[clickedList].name }}</h4>
                    </div>
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col lists-container">
                    <div v-if="isChoosingList" class="list-preview-container">
                        <div v-for="(list, i) in lists" @click="fetchList(i)"
                             class="row list-preview-container">
                            <div class="col">
                                <div class="card list-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col list-name-container">
                                                <span class="list-name">{{ list.name }}</span>
                                            </div>
                                        </div>
                                        <div class="row no-gutters">
                                            <div class="col-1 list-creator-avatar-container">
                                                <img class="list-creator-avatar" :src="list.user.profile_image_url"
                                                     :alt="'Imagen de perfil de @' + list.user.screen_name">
                                            </div>
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col">
                                                        <a :href="'https://twitter.com/' + list.user.screen_name"
                                                           class="list-author">
                                                            <span class="name">
                                                                {{ list.user.name }}
                                                            </span>
                                                            <span class="screen-name text-muted">
                                                                @{{ list.user.screen_name }}
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="row no-gutters animated slideInRight fastest tweet-list-row">
                        <div class="col tweet-list-container">
                            <tweet v-for="(tweet, i) in lists[clickedList].tweets" :tweet="tweet" :key="tweet.id" :user="user"></tweet>
                        </div>
                    </div>
                </div>
                <div v-if="showSpinner" class="spinner-container">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'lists',
            'user'
        ],
        data() {
            return {
                isChoosingList: true,
                clickedList: null,
                showSpinner: false
            }
        },
        methods: {
            goBack() {
                this.clickedList = null;
            },
            fetchList(i) {
                this.clickedList = i;

                if (!('tweets' in this.lists[this.clickedList])) {
                    this.showSpinner = true;

                    axios.get('/ajax/list/fetch', {
                        params: {
                            'id_str': this.lists[i].id_str
                        }
                    }).then((response) => {
                        this.lists[i].tweets = response.data;
                        this.isChoosingList = false;
                        this.showSpinner = false;
                    });
                } else {
                    this.isChoosingList = false;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .title-with-back-button {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 0.5rem;

        i {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 26px;
            height: 26px;

            cursor: pointer;
            transition: 250ms;
            border-radius: 50%;

            padding-right: 4px;
            margin-right: 0.5em;
            margin-left: -0.5em;

            &:hover {
                background-color: lighten($primaryColor, 30%);
            }
        }

        :not(i) {
            margin-bottom: 0;
        }
    }

    .fastest {
        -webkit-animation-duration: 200ms;
        animation-duration: 200ms;
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
        }
    }

    .lists-column-container {
        height: 100%;

        .lists-container {
            .list-preview-container {
                cursor: pointer;

                .card {
                    border-radius: 0;

                    .card-body {
                        padding: 15px;

                        .list-name-container {
                            margin-bottom: 0.3em;

                            .list-name {
                                font-size: 1.2rem;
                                margin-bottom: 0.5em;
                            }
                        }

                        .list-creator-avatar-container {
                            margin-right: 0.5em;

                            .list-creator-avatar {
                                width: 100%;
                                border-radius: 50%;
                                margin-right: 1em;
                            }
                        }

                        .list-author {
                            &:hover {
                                .name {
                                    text-decoration: underline;
                                }
                            }

                            .name {
                                font-weight: bold !important;
                                color: $textColor;
                            }

                            .screen-name {
                                font-weight: normal;
                                color: #a7a2ce;
                            }
                        }

                        .list-preview-message {

                        }

                        .list-options {
                            margin-top: 10px;

                            .list-action {
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
                    .list-card {
                        border-top: none;
                    }
                }
            }
        }

        .spinner-container {
            position: absolute;
            height: 100%;
            width: 100%;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #ffffffd1!important;
            z-index: 1;
        }

        &:not(:first-child) {
            .list-preview-container {
                border-top: none;
            }
        }
    }
</style>
