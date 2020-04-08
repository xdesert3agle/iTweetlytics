<template>
    <div class="row tweet-container">
        <div class="col-md-12 tweet-wrapper">
            <div class="card tweet-card">
                <div class="card-body">
                    <div v-if="tweet.retweeted_status" class="row text-muted">
                        <div class="col-2 text-right">
                            <i class="fa fa-retweet"></i>
                        </div>
                        <div class="col">
                            <span>{{ tweet.retweeted_status.user.name }} retwitteó</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <img :src="tweet.user.profile_image_url"
                                 class="tweet-user-avatar"
                                 :alt="'Imagen de perfil de @' + tweet.user.screen_name">
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <span class="name">{{ tweet.user.name }}</span>
                                    <span class="screen-name text-muted">@{{ tweet.user.screen_name }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span class="tweet-text">{{ tweet.retweeted_status ? tweet.retweeted_status.full_text : tweet.full_text }}</span>
                                </div>
                            </div>
                            <div class="row tweet-options" :class="{'favorited-tweet': tweet.favorited, 'retweeted-tweet': tweet.retweeted }">
                                <div class="col tweet-action action-comment">
                                    <i class="fa fa-comment"></i>
                                </div>

                                <div ref="actionRetweet" @click="toggleRetweet" class="col tweet-action action-retweet" :class="{'retweeted': tweet.retweeted}">
                                    <i class="fa fa-retweet"></i>
                                    <span v-if="tweet.retweet_count > 0">{{ tweet.retweet_count }}</span>
                                </div>

                                <div ref="actionFavorite" @click="toggleLike" class="col tweet-action action-like" :class="{'liked': tweet.liked}">
                                    <i class="fa fa-heart"></i>
                                    <span v-if="tweet.retweeted_status && tweet.retweeted_status.favorite_count > 0">{{ tweet.retweeted_status.favorite_count }}</span>
                                    <span v-else-if="!tweet.retweeted_status && tweet.favorite_count > 0">{{ tweet.favorite_count }}</span>
                                </div>

                                <div class="col tweet-action action-share">
                                    <i class="fa fa-share-alt"></i>
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
            'tweet'
        ],
        computed: {
            retweetRoute() {
                return '/ajax/tweets/retweet';
            },
            removeRetweetRoute() {
                return '/ajax/tweets/retweet/remove';
            },
            likeRoute() {
                return '/ajax/tweets/favorite';
            },
            removeLikeRoute() {
                return '/ajax/tweets/favorite/remove';
            },
        },
        methods: {
            toggleRetweet() {
                let targetRoute = this.tweet.retweeted ? this.removeRetweetRoute : this.retweetRoute;
                axios.post(targetRoute, { id: this.tweet.id_str }).then((response) => {
                    this.$refs.actionRetweet.toggleClass('retweeted');
                });
            },
            toggleLike() {
                let targetRoute = this.tweet.favorited ? this.removeLikeRoute : this.likeRoute;
                axios.post(targetRoute, { id: this.tweet.id_str }).then((response) => {
                    this.$refs.actionFavorite.toggleClass('liked');
                });
            },
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

                    .tweet-text {

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
