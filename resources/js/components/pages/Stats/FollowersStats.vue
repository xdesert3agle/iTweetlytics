<template>
    <div class="row">
        <div class="col-12 profile-stats-container">
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <graph-card id="followers" :user="user" :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/followers/'" card_title="Seguidores" modal_title="Seguidores">
                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(follower, i) in d_user.current_twitter_profile[0].followers" :id="'element-' + follower.screen_name">
                                    <div class="row profile-link">
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
                                        <div v-if="d_user.current_twitter_profile[0].friends[follower.id_str]" class="col-4">
                                            <button @click="unfollowUser(follower.screen_name, i)" class="btn btn-sm btn-unfollow">
                                                Dejar de seguir
                                            </button>
                                        </div>
                                        <div v-else class="col-4">
                                            <button @click="followUser(follower.screen_name, i)" class="btn btn-sm btn-follow">
                                                Seguir
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="follows" :user="user" :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/follows/'" card_title="Nuevos seguidores" modal_title="Nuevos seguidores">
                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="unfollows" :user="user" :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/unfollows/'" card_title="Unfollowers" modal_title="Unfollowers">
                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>
                    </graph-card>
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
            },
            followUser(screen_name, index) {
                axios.post('/ajax/profile/follow', {
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

            @media (max-width: 768px) {
                div[class*="col"]:not(:first-child) {
                    margin-top: 10px!important;
                    padding-left: 0!important;
                }
            }

            .stat-amount {
                font-size: 32pt;
                font-weight: bold;
                color: $primaryColor;
                line-height: initial;
                display: flex;
                flex: 1;
                flex-direction: column-reverse;
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
