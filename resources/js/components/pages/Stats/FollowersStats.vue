<template>
    <div class="row no-gutters">
        <div class="col-md-5 col-12">
            <div class="row card-row">
                <div class="col">
                    <graph-card id="followers"
                                :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/followers/'"
                                card_title="Seguidores"
                                modal_title="Seguidores">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(follower, i) in d_user.current_twitter_profile[0].followers" :id="'element-' + follower.screen_name">
                                    <div class="row no-gutters profile-link">
                                        <a :href="'https://twitter.com/' + follower.screen_name" class="col-auto">
                                            <img :src="follower.profile_image_url" :alt="'Foto de perfil de @' + follower.screen_name">
                                        </a>
                                        <div class="col">
                                            <span class="name">
                                                <a :href="'https://twitter.com/' + follower.screen_name">
                                                    {{ follower.name }}
                                                </a>
                                            </span>
                                            <span class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                        </div>
                                        <div class="col-auto unfollow-button">
                                            <i @click="unfollowUser(follower.screen_name, i)" class="fa fa-lg fa-times"></i>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
            </div>
            <div class="row card-row">
                <div class="col">
                    <graph-card id="unfollows"
                                :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/unfollows/'"
                                card_title="Unfollowers"
                                modal_title="Unfollowers">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(unfollower, i) in d_user.current_twitter_profile[0].unfollows" :id="'element-' + unfollower.screen_name">
                                    <div class="row no-gutters profile-link">
                                        <a :href="'https://twitter.com/' + unfollower.screen_name" class="col-auto">
                                            <img :src="unfollower.profile_image_url" :alt="'Foto de perfil de @' + unfollower.screen_name">
                                        </a>
                                        <div class="col">
                                            <span class="name">
                                                <a :href="'https://twitter.com/' + unfollower.screen_name">
                                                    {{ unfollower.name }}
                                                </a>
                                            </span>
                                            <span class="screen-name text-muted">@{{ unfollower.screen_name }}</span>
                                        </div>
                                        <div class="col-auto unfollow-button">
                                            <i @click="unfollowUser(unfollower.screen_name, i)" class="fa fa-lg fa-times"></i>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="row card-row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Follow-back a tus seguidores</h4>
                            <div class="row card-content">
                                <div class="col">
                                    <span class="stat-amount">{{ d_user.current_twitter_profile[0].reports[d_user.current_twitter_profile[0].reports.length - 1].user_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
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
            'user'
        ],
        data() {
            return {
                d_user: this.user
            }
        },
        methods: {
            unfollowUser(screen_name, index) {
                axios.post('/ajax/profile/unfollow', {
                    'screen_name': screen_name,
                    'twitter_profile_id': this.d_user.current_twitter_profile[0].id
                }).then((response) => {
                    if (response.data.status == 'success') {
                        this.twitterProfile = response.data.data;
                        this.$toast.success(response.data.message);

                        this.d_user.current_twitter_profile[0].friends.splice(index, 1);
                        $('#element-' + screen_name).remove();
                    } else {
                        this.$toast.error(response.data.message);
                    }
                });
            }
        }
    }
</script>


<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .profile-stats-container {

        div[class*="col"] {
            display: flex;
            flex-direction: column;

            &:not(:first-child) {
                padding-left: 10px;
            }
        }

        .card-row {
            //flex: 1;

            .stat-amount {
                font-size: 32pt;
                font-weight: bold;
                color: $primaryColor;
                line-height: initial;
                display: flex;
                flex: 1;
                flex-direction: column-reverse;
            }

            .profiles-list {
                li {
                    &:not(:first-child) {
                        margin-top: 10px;
                    }

                    .profile-link {

                        a {
                            text-decoration: none;
                        }

                        img {
                            border-radius: 50%;
                        }

                        .name {
                            display: flex;
                            align-items: center;

                            font-weight: bold !important;
                            line-height: initial;

                            > :first-child {
                                color: $textColor;
                            }

                            span.badge {
                                margin-left: 7px;
                            }
                        }

                        .screen-name {
                            font-weight: normal;
                            display: block;
                            color: #a7a2ce;
                            margin-top: 4px;
                            line-height: initial;
                        }
                    }

                    .unfollow-button {
                        display: flex;
                        justify-content: center;
                        cursor: pointer;
                        transition: 100ms;
                        color: gray;

                        &:hover {
                            color: red;
                        }
                    }
                }
            }

            &:not(:first-child) {
                margin-top: 10px;
            }
        }
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
</style>
