<template>
    <a :href="'https://twitter.com/' + updatedTweet.user.screen_name + '/status/' + updatedTweet.id_str"
       class="row tweet-container">
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
                            <img
                                :src="!updatedTweet.retweeted_status ? updatedTweet.user.profile_image_url : updatedTweet.retweeted_status.user.profile_image_url"
                                class="tweet-user-avatar"
                                :alt="'Imagen de perfil de @' + updatedTweet.user.screen_name">
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <div class="col">
                                    <a v-if="!updatedTweet.retweeted_status"
                                       :href="'https://twitter.com/' + updatedTweet.user.screen_name"
                                       class="tweet-author">
                                        <span class="name">
                                            {{ updatedTweet.user.name }}
                                        </span>
                                        <span class="screen-name text-muted">
                                            @{{ updatedTweet.user.screen_name }}
                                        </span>
                                    </a>
                                    <a v-else
                                       :href="'https://twitter.com/' + updatedTweet.retweeted_status.user.screen_name"
                                        class="tweet-author">
                                        <span class="name">
                                            {{ updatedTweet.retweeted_status.user.name }}
                                            </span>
                                        <span class="screen-name text-muted">
                                            @{{ updatedTweet.retweeted_status.user.screen_name }}
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="tweet-text"
                                          v-html="updatedTweet.retweeted_status ? linkifyEntities(updatedTweet.retweeted_status) : linkifyEntities(updatedTweet)"></div>

                                    <expandable-image
                                        @click.native.prevent
                                        class="tweet-media-image"
                                        v-if="updatedTweet.entities.media && !hasVideo"
                                        :src="updatedTweet.entities.media[0].media_url_https"
                                        closeOnBackgroundClick></expandable-image>

                                    <vue-plyr v-if="hasVideo" @click.native.prevent>
                                        <video>
                                            <source :src="updatedTweet.extended_entities.media[0].video_info.variants[0].url">
                                        </video>
                                    </vue-plyr>

                                </div>
                            </div>
                            <div class="row tweet-options"
                                 :class="{'favorited-tweet': updatedTweet.favorited, 'retweeted-tweet': updatedTweet.retweeted }">
                                <div ref="actionComment" class="col tweet-action action-comment">
                                    <i class="fa fa-comment"></i>
                                </div>

                                <div ref="actionRetweet" @click.prevent="toggleRetweet"
                                     class="col tweet-action action-retweet"
                                     :class="{'retweeted': updatedTweet.retweeted}">
                                    <i class="fa fa-retweet"></i>
                                    <span v-if="updatedTweet.retweet_count > 0">{{ updatedTweet.retweet_count }}</span>
                                </div>

                                <div ref="actionFavorite" @click.prevent="toggleLike"
                                     class="col tweet-action action-like"
                                     :class="{'liked': updatedTweet.liked}">
                                    <i class="fa fa-heart"></i>
                                    <span
                                        v-if="updatedTweet.retweeted_status && updatedTweet.retweeted_status.favorite_count > 0">{{ updatedTweet.retweeted_status.favorite_count }}</span>
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
    </a>
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
            hasVideo() {
                var hasVideo = false;

                if (this.updatedTweet.extended_entities && this.updatedTweet.extended_entities.media) {

                    for (let i = 0; i < this.updatedTweet.extended_entities.media.length; i++) {
                        if (this.updatedTweet.extended_entities.media[i].type == "video") {
                            hasVideo = true;
                            break;
                        }
                    }
                }

                return hasVideo;
            }
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

                axios.post(targetRoute, {id: targetId}).then((response) => {
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

                axios.post(targetRoute, {id: this.updatedTweet.id_str}).then((response) => {
                    this.updatedTweet = response.data;
                });
            },
            escapeHTML(text) {
                return $('<div/>').text(this.htmlCharsCorrect(text)).html();
            },
            htmlCharsCorrect(text) {
                text = text.replace(/&amp;/g, '\u0026');
                text = text.replace(/&gt;/g, '\u003E');
                text = text.replace(/&lt;/g, '\u003C');
                text = text.replace(/&(quot;|apos;)/g, '\u0022');
                text = text.replace(/&#039;+/g, '\u0027');
                return text;
            },
            linkifyEntities(tweet) {
                var
                    index_map = {},
                    result = "",
                    last_i = 0,
                    i = 0,
                    end,
                    func,
                    emoji;

                var ranges = [
                    '\ud83c[\udf00-\udfff]', // U+1F300 to U+1F3FF
                    '\ud83d[\udc00-\ude4f]', // U+1F400 to U+1F64F
                    '\ud83d[\ude80-\udeff]'  // U+1F680 to U+1F6FF
                ];

                var emojis = [];

                tweet.full_text = this.escapeHTML(tweet.full_text.replace(new RegExp(ranges.join('|'), 'g'), (match, offset, string) => {
                    emojis.push({
                        offset: offset,
                        char: match
                    });
                    return '\u0091';
                }));

                if (!(tweet.entities)) {
                    return this.escapeHTML(tweet.full_text);
                }

                if (tweet.entities.urls) {
                    $.each(tweet.entities.urls, (i, entry) => {
                        index_map[entry.indices[0]] = [entry.indices[1], (text) => {
                            return "<a class='tweet-text-entity' href='" + this.escapeHTML(entry.url) + "'>" + this.escapeHTML(entry.display_url) + "</a>";
                        }];
                    });
                }

                if (tweet.entities.hashtags) {
                    $.each(tweet.entities.hashtags, (i, entry) => {
                        index_map[entry.indices[0]] = [entry.indices[1], (text) => {
                            return "<a class='tweet-text-entity' href='http://twitter.com/search?q=" + escape("#" + entry.text) + "'>" + this.escapeHTML(text) + "</a>";
                        }];
                    });
                }

                if (tweet.entities.user_mentions) {
                    $.each(tweet.entities.user_mentions, (i, entry) => {
                        index_map[entry.indices[0]] = [entry.indices[1], (text) => {
                            return "<a class='tweet-text-entity' title='" + this.escapeHTML(entry.name) + "' href='http://twitter.com/" + this.escapeHTML(entry.screen_name) + "'>" + this.escapeHTML(text) + "</a>";
                        }];
                    });
                }

                if (tweet.entities.hasOwnProperty('media')) {
                    $.each(tweet.entities.media, (i, entry) => {
                        index_map[entry.indices[0]] = [entry.indices[1], (text) => {
                            return "";
                        }];
                    });
                }

                for (i = 0; i < tweet.full_text.length; ++i) {
                    var ind = index_map[i];
                    if (ind) {
                        end = ind[0];
                        func = ind[1];
                        if (i > last_i) {
                            result += this.escapeHTML(tweet.full_text.substring(last_i, i));
                        }
                        result += func(tweet.full_text.substring(i, end));
                        i = end - 1;
                        last_i = end;
                    }
                }

                if (i > last_i) {
                    result += this.escapeHTML(tweet.full_text.substring(last_i, i));
                }

                result = result.replace(/\u0091/g, (match, offset, string) => {
                    emoji = emojis.shift();

                    return '<span class="emoji">' + emoji.char + '</span>'
                });

                return result;
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

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

                    .tweet-user-avatar {
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
                            font-weight: bold!important;
                        }
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

        &:last-child {
            .tweet-card {
                border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            }
        }
    }
</style>
