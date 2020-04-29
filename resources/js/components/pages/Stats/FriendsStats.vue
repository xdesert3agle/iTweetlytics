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
                                                    <h4 class="card-title">Friends</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchFriendsData($event.target.value)">
                                                        <option value="weekly" selected>7 días
                                                        </option>
                                                        <option value="biweekly">14 días
                                                        </option>
                                                        <option value="monthly">30 días</option>
                                                        <option value="yearly">1 año</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="friends-list-count-container">
                                                <span>Siguiendo {{ user.current_twitter_profile[0].friends.length }}</span>
                                                <a href="" data-toggle="modal" data-target="#friends-modal">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>

                                            <div class="modal fade" id="friends-modal" tabindex="-1" role="dialog" aria-labelledby="friends-modal-label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="friends-modal-label">
                                                                Seguidores</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="profiles-list">
                                                                <li v-for="(follower, i) in user.current_twitter_profile[0].friends">
                                                                    <div class="row no-gutters profile-link">
                                                                        <a :href="'https://twitter.com/' + follower.screen_name" class="col-auto">
                                                                            <img :src="follower.profile_image_url" :alt="'Foto de perfil de @' + follower.screen_name">
                                                                        </a>
                                                                        <div class="col">
                                                                            <a :href="'https://twitter.com/' + follower.screen_name" class="name">{{
                                                                                follower.name }}</a>
                                                                            <span class="screen-name text-muted">@{{ follower.screen_name }}</span>
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
                                            <line-chart :data="friendsData" width="100%"></line-chart>
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
                                                    <h4 class="card-title">Unfriends</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchUnfollowsData($event.target.value)">
                                                        <option value="weekly" selected>7 días</option>
                                                        <option value="biweekly">14 días</option>
                                                        <option value="monthly">30 días</option>
                                                        <option value="yearly">1 año</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="unfriends-list-count-container">
                                                <div v-if="user.current_twitter_profile[0].unfriends">
                                                    <span>{{ user.current_twitter_profile[0].unfriends.length }} unfollows</span>
                                                    <a href="" data-toggle="modal" data-target="#unfriends-modal">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                                <span v-else>Ningún unfollower reciente</span>
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
                            <h4 class="card-title">Tu follow-back</h4>
                            <div class="row card-content">
                                <div class="col">
                                    <span class="stat-amount">{{ user.current_twitter_profile[0].reports[user.current_twitter_profile[0].reports.length - 1].user_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
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
                friendsData: null,
                unfollowsData: null
            }
        },
        created() {
            this.fetchFriendsData('weekly');
            this.fetchUnfollowsData('weekly');
        },
        methods: {
            fetchFriendsData(timeInterval) {
                axios.get('/ajax/profile/' + this.user.current_twitter_profile[0].id + '/stats/friends/' + timeInterval + '/')
                    .then((response) => {
                        this.friendsData = response.data;
                    });
            },
            fetchUnfollowsData(timeInterval) {
                axios.get('/ajax/profile/' + this.user.current_twitter_profile[0].id + '/stats/unfollows/' + timeInterval + '/')
                    .then((response) => {
                        this.unfollowsData = response.data;
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

                    .friends-list-count-container, .unfriends-list-count-container {
                        margin-bottom: 2em;

                        a {
                            color: inherit;

                            &:hover {
                                text-decoration: none;
                            }
                        }
                    }

                    .recent-unfriends-container {
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

                    #friends-modal, #unfriends-modal {
                        .modal-body {
                            max-height: 400px;
                            overflow-y: scroll;
                            overflow-x: hidden;
                        }
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
