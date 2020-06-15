<template>
    <div>
        <a :href="'https://twitter.com/' + updatedTweet.user.screen_name + '/status/' + updatedTweet.id_str" class="row tweet-container">
            <div class="col-md-12 tweet-wrapper">
                <div class="card tweet-card">
                    <div class="card-body">
                        <div v-if="updatedTweet.retweeted_status" class="row no-gutters text-muted">
                            <div class="col-2 rt-icon-container">
                                <i class="fa fa-retweet"></i>
                            </div>
                            <div class="col rt-author">
                                <span>{{ updatedTweet.user.name }} retwitteó</span>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col-2 tweet-user-avatar-container">
                                <img :src="!updatedTweet.retweeted_status ? updatedTweet.user.profile_image_url : updatedTweet.retweeted_status.user.profile_image_url" class="tweet-user-avatar" :alt="'Imagen de perfil de @' + updatedTweet.user.screen_name">
                            </div>
                            <div class="col-10 tweet-content-container">
                                <div class="row">
                                    <div class="col">
                                        <a v-if="!updatedTweet.retweeted_status" :href="'https://twitter.com/' + updatedTweet.user.screen_name" class="tweet-author">
                                        <span class="name">
                                            {{ updatedTweet.user.name }}
                                        </span>
                                            <span class="screen-name text-muted">
                                            @{{ updatedTweet.user.screen_name }}
                                        </span>
                                        </a>
                                        <a v-else :href="'https://twitter.com/' + updatedTweet.retweeted_status.user.screen_name" class="tweet-author">
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
                                        <div class="tweet-text" v-html="linkifiedTweet"></div>

                                        <expandable-image @click.native.prevent class="tweet-media-image" v-if="updatedTweet.entities.media && !hasVideo" :src="updatedTweet.entities.media[0].media_url_https" closeOnBackgroundClick></expandable-image>

                                        <vue-plyr v-if="hasVideo" @click.native.prevent>
                                            <video>
                                                <source :src="updatedTweet.extended_entities.media[0].video_info.variants[0].url">
                                            </video>
                                        </vue-plyr>

                                    </div>
                                </div>
                                <div class="row tweet-options" :class="{'favorited-tweet': updatedTweet.favorited, 'retweeted-tweet': updatedTweet.retweeted }">
                                    <div ref="actionComment" class="col tweet-action action-comment" @click.prevent>
                                        <i class="fa fa-comment" data-toggle="modal" :data-target="'#new-reply-modal' + tweet.id"></i>
                                    </div>

                                    <div ref="actionRetweet" @click.prevent="toggleRetweet" class="col tweet-action action-retweet" :class="{'retweeted': updatedTweet.retweeted}">
                                        <i class="fa fa-retweet"></i>
                                        <span v-if="updatedTweet.retweet_count > 0">{{ updatedTweet.retweet_count }}</span>
                                    </div>

                                    <div ref="actionFavorite" @click.prevent="toggleLike" class="col tweet-action action-like" :class="{'liked': updatedTweet.liked}">
                                        <i class="fa fa-heart"></i>
                                        <span v-if="updatedTweet.retweeted_status && updatedTweet.retweeted_status.favorite_count > 0">{{ updatedTweet.retweeted_status.favorite_count }}</span>
                                        <span v-else-if="!updatedTweet.retweeted_status && updatedTweet.favorite_count > 0">{{ updatedTweet.favorite_count }}</span>
                                    </div>

                                    <div id="btn-share" @click.prevent="copyTweetToClipboard" class="col tweet-action action-share">
                                        <i data-toggle="tooltip" data-placement="top" title="Tweet copiado" class="fa fa-share-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <!-- Modal -->
        <div class="modal fade" :id="'new-reply-modal' + tweet.id" tabindex="-1" role="dialog" :aria-labelledby="'label-new-reply-modal' + tweet.id" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" :id="'label-new-reply-modal' + tweet.id">Respondiendo a @{{
                            !tweet.retweeted_status ? tweet.user.screen_name : tweet.retweeted_status.user.screen_name
                            }}</h5>
                        <button type="button" :id="'close-new-reply-modal' + tweet.id" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row no-gutters">
                            <div class="col-2 reply-tweet-user-avatar-container">
                                <img :src="!tweet.retweeted_status ? tweet.user.profile_image_url : tweet.retweeted_status.user.profile_image_url" class="tweet-user-avatar" :alt="'Imagen de perfil de @' + tweet.user.screen_name">
                            </div>
                            <div class="col-10 reply-tweet-content-container">
                                <div class="tweet-text" v-html="linkifiedTweet"></div>
                                <expandable-image @click.native.prevent class="tweet-media-image" v-if="updatedTweet.entities.media && !hasVideo" :src="updatedTweet.entities.media[0].media_url_https" closeOnBackgroundClick></expandable-image>
                            </div>
                        </div>
                        <div class="row no-gutters reply-container">
                            <div class="col-2 reply-user-profile-img-container">
                                <img :src="user.current_user_profile.twitter_profile.profile_image_url" class="reply-user-profile-img" alt="Tu imagen de perfil">
                            </div>
                            <div class="col-10 reply-tweet-content-container">
                                <textarea class="js-autoresize" v-model="replyText" maxlength="280" :placeholder="'Respondiendo a ' + !tweet.retweeted_status ? tweet.user.screen_name : tweet.retweeted_status.user.screen_name"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="sendReply" type="button" class="btn btn-primary btn-round">Enviar respuesta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueExpandableImage from 'vue-expandable-image'
    import VuePlyr from 'vue-plyr'
    import {setResizeListeners} from "../../../../helpers/auto-resize.js";

    export default {
        props: [
            'tweet',
            'user',
        ],
        components: {
            VueExpandableImage,
            VuePlyr
        },
        data() {
            return {
                updatedTweet: this.tweet,
                response: [],
                linkifiedTweet: null,
                replyText: null
            }
        },
        created() {
            this.linkifyCurrentTweet();
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
        mounted() {
            $('i[data-toggle="tooltip"]').tooltip({
                animated: 'fade',
                placement: 'top',
                trigger: 'click'
            });
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
            },
            linkifyCurrentTweet() {
                this.linkifiedTweet = this.updatedTweet.retweeted_status ? this.linkifyEntities(this.updatedTweet.retweeted_status) : this.linkifyEntities(this.updatedTweet)
            },
            copyTweetToClipboard() {
                var dummy = document.createElement("textarea");
                document.body.appendChild(dummy);
                dummy.value = 'https://twitter.com/' + this.updatedTweet.user.screen_name + '/status/' + this.updatedTweet.id_str;
                dummy.select();
                document.execCommand("copy");
                document.body.removeChild(dummy);

                setTimeout(function () {
                    $('i[data-toggle="tooltip"]').tooltip('hide');
                }, 1250);
            },
            sendReply() {
                axios.post('/ajax/tweets/sendReply', {
                    'tweet_content': this.replyText,
                    'target_id': this.tweet.id_str,
                    'target_screen_name': !this.tweet.retweeted_status ? this.tweet.user.screen_name : this.tweet.retweeted_status.user.screen_name
                }).then((response) => {
                    let toastType;

                    if (response.data.status == 'success') {
                        toastType = 'success';
                        this.replyText = "";
                    } else {
                        toastType = 'error';
                    }

                    this.$toastr.Add({
                        msg: response.data.message,
                        clickClose: true,
                        timeout: 3000,
                        type: toastType,
                        preventDuplicates: true,
                        classNames: ["animated", "slideInRight", "ms-300"],
                    });
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;
    $tweetColor: #212529;

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
            .reply-container {
                padding-top: 2em;
                margin-top: 2em;
                border-top: 1px solid rgba(0, 0, 0, 0.1);

                .reply-user-profile-img-container {
                    padding-right: 7.5px !important;

                    .reply-user-profile-img {
                        width: 100% !important;
                        border-radius: 50% !important;
                    }

                    .tweet-media-image {
                        width: 100%!important;
                        margin-top: 10px!important;
                    }
                }
            }

            .reply-tweet-user-avatar-container {
                padding-right: 7.5px !important;

                .tweet-user-avatar {
                    width: 100% !important;
                    border-radius: 50% !important;
                }
            }

            .reply-tweet-content-container {
                padding-left: 7.5px !important;

                .tweet-text {
                    font-size: 14pt !important;
                    font-weight: normal;
                    color: $tweetColor;
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
                display: block !important;
                padding-right: 5px;

                font-weight: 500;
                text-align: right;
                color: $textColor;
            }
        }
    }

    a:hover {
        text-decoration: none;
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

                    .row.text-muted {
                        margin-bottom: 5px;

                        .rt-icon-container {
                            display: flex;
                            justify-content: center;
                            align-items: center;

                            margin-right: 7.5px;
                            padding-right: 10px;
                        }
                    }

                    .tweet-user-avatar-container {
                        padding-right: 7.5px;

                        .tweet-user-avatar {
                            width: 100%;
                            border-radius: 50%;
                        }
                    }

                    .tweet-content-container {
                        padding-left: 7.5px;

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

        &:last-child {
            .tweet-card {
                border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            }
        }
    }

    @media (max-width: 576px) {
        .rt-author {
            font-size: 12pt !important;
        }

        .name, .screen-name {
            font-size: 12pt !important;
        }

        .tweet-text {
            font-size: 12pt !important;

            &-entity {
                font-weight: bold !important;
            }
        }
    }

    @media (max-width: 1280px) {
        .rt-author {
            font-size: 11pt;
        }

        .tweet-user-avatar {
            margin-top: 5px;
            max-width: 60px;
        }

        .name, .screen-name {
            font-size: 11pt;
        }

        .tweet-text {
            font-size: 11pt;

            &-entity {
                font-weight: bold !important;
            }
        }
    }
</style>
