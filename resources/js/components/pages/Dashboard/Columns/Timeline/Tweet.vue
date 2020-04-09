<template>
    <div class="row tweet-container">
        <div class="col-md-12 tweet-wrapper">
            <div class="card tweet-card">
                <div class="card-body">
                    <div v-if="updatedTweet.retweeted_status" class="row text-muted">
                        <div class="col-2 text-right">
                            <i class="fa fa-retweet"></i>
                        </div>
                        <div class="col">
                            <span>{{ updatedTweet.user.name }} retwitteó</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <img :src="!updatedTweet.retweeted_status ? updatedTweet.user.profile_image_url : updatedTweet.retweeted_status.user.profile_image_url"
                                 class="tweet-user-avatar"
                                 :alt="'Imagen de perfil de @' + updatedTweet.user.screen_name">
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <span class="name">{{ !updatedTweet.retweeted_status ? updatedTweet.user.name : updatedTweet.retweeted_status.user.name }}</span>
                                    <span class="screen-name text-muted">@{{ !updatedTweet.retweeted_status ? updatedTweet.user.screen_name : updatedTweet.retweeted_status.user.screen_name }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span class="tweet-text">{{ updatedTweet.retweeted_status ? updatedTweet.retweeted_status.full_text : updatedTweet.full_text }}</span>
                                </div>
                            </div>
                            <div class="row tweet-options" :class="{'favorited-tweet': updatedTweet.favorited, 'retweeted-tweet': updatedTweet.retweeted }">
                                <div class="col tweet-action action-comment">
                                    <i class="fa fa-comment"></i>
                                </div>

                                <div ref="actionRetweet" @click="toggleRetweet" class="col tweet-action action-retweet" :class="{'retweeted': updatedTweet.retweeted}">
                                    <i class="fa fa-retweet"></i>
                                    <span v-if="updatedTweet.retweet_count > 0">{{ updatedTweet.retweet_count }}</span>
                                </div>

                                <div ref="actionFavorite" @click="toggleLike" class="col tweet-action action-like" :class="{'liked': updatedTweet.liked}">
                                    <i class="fa fa-heart"></i>
                                    <span v-if="updatedTweet.retweeted_status && updatedTweet.retweeted_status.favorite_count > 0">{{ updatedTweet.retweeted_status.favorite_count }}</span>
                                    <span v-else-if="!updatedTweet.retweeted_status && updatedTweet.favorite_count > 0">{{ updatedTweet.favorite_count }}</span>
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
        data() {
            return {
                updatedTweet: this.tweet,
                response: []
            }
        },
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
                let targetRoute, targetId;

                if (!this.updatedTweet.retweeted) {
                    targetRoute = this.retweetRoute;
                    targetId = this.updatedTweet.id_str;
                } else {
                    targetRoute = this.removeRetweetRoute;
                    targetId = this.updatedTweet.retweeted_status.id_str;
                }

                $(this.$refs.actionRetweet).toggleClass('retweeted');

                axios.post(targetRoute, { id: targetId }).then((response) => {
                    this.updatedTweet = response.data;
                });
            },
            toggleLike() {
                let targetRoute;
                if (!this.updatedTweet.favorited) {
                    targetRoute = this.likeRoute;
                } else {
                    targetRoute = this.removeLikeRoute;
                }

                $(this.$refs.actionFavorite).toggleClass('liked');

                axios.post(targetRoute, { id: this.updatedTweet.id_str }).then((response) => {
                    this.updatedTweet = response.data;
                });
            }
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
