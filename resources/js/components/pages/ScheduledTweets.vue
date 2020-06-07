<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Tweets programados de los próximos días</h3>
                    </div>
                </div>

                <div v-if="user.current_user_profile.scheduled_tweets != null" class="row no-gutters scheduled-tweets-container">
                    <div class="col-3" v-for="(tweetGroup, date) in user.current_user_profile.scheduled_tweets">
                        <h5>{{ date }}</h5>
                        <div v-for="(tweet, j) in tweetGroup" class="row tweet-container">
                            <div class="col-md-12 tweet-wrapper">
                                <div class="card tweet-card">
                                    <div class="card-body">
                                        <div class="row no-gutters">
                                            <div class="col-2">
                                                <img :src="user.current_user_profile.twitter_profile.profile_image_url" class="tweet-user-avatar" alt="Tu avatar">
                                            </div>
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col">
                                                        <a :href="'https://twitter.com/' + user.current_user_profile.twitter_profile.screen_name" class="tweet-author">
                                                            <span class="name">{{ user.current_user_profile.twitter_profile.name }}</span>
                                                            <span class="screen-name text-muted">
                                                                @{{ user.current_user_profile.twitter_profile.screen_name }}
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button @click="deleteScheduledTweet(tweet.id)" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <span class="tweet-text">{{ tweet.tweet_content }}</span>
                                                    </div>
                                                    <div class="col-auto tweet-schedule-hour">
                                                        <span class="text-muted">{{ tweet.schedule_hour }}</span>
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
                <div v-else class="row">
                    <div class="col">
                        <span>No tienes ningún tweet programado. Prueba a programar uno haciendo click en el botón 'Tweet' en el Dashboard.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user'
        ],
        methods: {
            deleteScheduledTweet(tweetId) {
                axios.post('/ajax/profile/scheduled_tweet/delete', {
                    tweetId: tweetId
                }).then((response) => {
                    this.$toast.success('Has eliminado el tweet programado.');
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    a:hover {
        text-decoration: none;
    }

    .scheduled-tweets-container {
        div[class*="col"]:not(:first-child) {
            padding-left: 10px;
        }

        .tweet-container {
            color: inherit;

            .tweet-wrapper {
                .tweet-card {
                    border-radius: 0;
                    border-left: 0;
                    border-right: 0;
                    border-top: none;

                    .card-body {
                        padding: 15px;

                        .row {
                            [class*="col"] {
                                &:not(:first-child) {
                                    margin-left: 15px;
                                }

                                .tweet-user-avatar {
                                    width: 100%;
                                    border-radius: 50%;
                                }

                                .tweet-author {
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

                                .tweet-text {
                                    &-entity {
                                        font-weight: bold !important;
                                    }
                                }

                                .tweet-schedule-hour {
                                    align-self: flex-end;
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
                }
            }

            &:last-child {
                .tweet-card {
                    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
                }
            }
        }
    }
</style>
