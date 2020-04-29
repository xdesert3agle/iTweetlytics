<template>
    <div class="row no-gutters">
        <div class="col-md-5 col-12">
            <div class="row card-row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <h4 class="card-title">Followers</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchFollowersData($event.target.value)">
                                                        <option value="weekly" selected>7 días
                                                        </option>
                                                        <option value="biweekly">14 días
                                                        </option>
                                                        <option value="monthly">30 días</option>
                                                        <option value="yearly">1 año</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="followers-list-count-container">
                                                <span>{{ d_user.current_twitter_profile[0].followers.length }} seguidores</span>
                                                <button-modal id="followers-list" title="Seguidores" :button="false">
                                                    <template slot="button">
                                                        <i class="fa fa-lg fa-eye"></i>
                                                    </template>
                                                    <template slot="modal-body">
                                                        <ul class="profiles-list">
                                                            <li v-for="(friend, i) in d_user.current_twitter_profile[0].friends">
                                                                <div class="row no-gutters profile-link">
                                                                    <a :href="'https://twitter.com/' + friend.screen_name" class="col-auto">
                                                                        <img :src="friend.profile_image_url" :alt="'Foto de perfil de @' + friend.screen_name">
                                                                    </a>
                                                                    <div class="col">
                                                                                    <span class="name">
                                                                                        <a :href="'https://twitter.com/' + friend.screen_name">{{ friend.name }}</a>
                                                                                        <span v-if="friend.follows_you" class="badge badge-success">Te sigue</span>
                                                                                        <span v-else class="badge badge-danger">No te sigue</span>
                                                                                    </span>
                                                                        <span class="screen-name text-muted">@{{ friend.screen_name }}</span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </template>
                                                </button-modal>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <line-chart :data="followersData" width="100%"></line-chart>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row card-row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <h4 class="card-title">Unfollowers</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchUnfollowsData($event.target.value)">
                                                        <option value="weekly" selected>7 días
                                                        </option>
                                                        <option value="biweekly">14 días
                                                        </option>
                                                        <option value="monthly">30 días</option>
                                                        <option value="yearly">1 año</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="unfollowers-list-count-container">
                                                <div v-if="d_user.current_twitter_profile[0].unfollowers">
                                                    <span>{{ d_user.current_twitter_profile[0].unfollowers.length }} unfollows</span>
                                                    <a href="" data-toggle="modal" data-target="#unfollowers-modal">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                                <span v-else>Ningún unfollower reciente</span>
                                            </div>

                                            <div class="modal fade" id="unfollowers-modal" tabindex="-1" role="dialog" aria-labelledby="unfollowers-modal-label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="unfollowers-modal-label">
                                                                Unfollowers</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="profiles-list">
                                                                <li v-for="(unfollower, i) in d_user.current_twitter_profile[0].unfollowers">
                                                                    <div class="row no-gutters profile-link">
                                                                        <a :href="'https://twitter.com/' + unfollower.screen_name" class="col-auto">
                                                                            <img :src="unfollower.profile_image_url" :alt="'Foto de perfil de @' + unfollower.screen_name">
                                                                        </a>
                                                                        <div class="col">
                                                                            <a :href="'https://twitter.com/' + unfollower.screen_name" class="name">{{
                                                                                unfollower.name
                                                                                }}</a>
                                                                            <span class="screen-name text-muted">@{{ unfollower.screen_name }}</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <line-chart :data="unfollowsData" width="100%"></line-chart>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="row card-row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Follow-back de tus seguidos</h4>
                            <div class="row card-content">
                                <div class="col">
                                    <span class="stat-amount">{{ d_user.current_twitter_profile[0].reports[d_user.current_twitter_profile[0].reports.length - 1].followers_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
                                </div>
                                <div class="col-auto">
                                    <button-modal id="followback-details" title="Seguidos" :button="true">
                                        <template slot="button">
                                            <span>Detalles</span>
                                        </template>
                                        <template slot="modal-body">
                                            <ul class="profiles-list">
                                                <li v-for="(friend, i) in d_user.current_twitter_profile[0].friends" :id="'element-' + friend.screen_name">
                                                    <div class="row no-gutters profile-link">
                                                        <a :href="'https://twitter.com/' + friend.screen_name" class="col-auto">
                                                            <img :src="friend.profile_image_url" :alt="'Foto de perfil de @' + friend.screen_name">
                                                        </a>
                                                        <div class="col">
                                                            <span class="name">
                                                                <a :href="'https://twitter.com/' + friend.screen_name">
                                                                    {{ friend.name }}
                                                                    <span v-if="friend.follows_you" class="badge badge-success">Te sigue</span>
                                                                    <span v-else class="badge badge-danger">No te sigue</span>
                                                                </a>
                                                            </span>
                                                            <span class="screen-name text-muted">@{{ friend.screen_name }}</span>
                                                        </div>
                                                        <div class="col-auto unfollow-button">
                                                            <i @click="unfollowUser(friend.screen_name, i)" class="fa fa-lg fa-times"></i>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </template>
                                    </button-modal>
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
                d_user: this.user,
                followersData: null,
                unfollowsData: null
            }
        },
        created() {
            this.fetchFollowersData('weekly');
            this.fetchUnfollowsData('weekly');
        },
        methods: {
            fetchFollowersData(timeInterval) {
                axios.get('/ajax/profile/' + this.d_user.current_twitter_profile[0].id + '/stats/followers/' + timeInterval + '/')
                    .then((response) => {
                        this.followersData = response.data;
                    });
            },
            fetchUnfollowsData(timeInterval) {
                axios.get('/ajax/profile/' + this.d_user.current_twitter_profile[0].id + '/stats/unfollows/' + timeInterval + '/')
                    .then((response) => {
                        this.unfollowsData = response.data;
                    });
            },
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

            .card {
                //height: 100%;

                .card-body {
                    display: flex;
                    flex-direction: column;
                    max-height: 100vh;
                    overflow: hidden;

                    .card-title {
                        font-size: 16pt;
                    }

                    .card-content {
                        align-items: center;
                    }

                    .followers-list-count-container, .unfollowers-list-count-container {
                        margin-bottom: 2em;

                        a {
                            color: inherit;

                            &:hover {
                                text-decoration: none;
                            }
                        }
                    }

                    .recent-unfollowers-container {
                        max-height: 300px;
                        overflow-y: scroll;

                        ::-webkit-scrollbar {
                            width: 10px !important;
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
