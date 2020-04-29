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
                                                    <h4 class="card-title">Sigues a</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchFriendsGraphData($event.target.value)">
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
                                                <span class="stat-amount">{{ user.current_twitter_profile[0].friends.length }}</span>
                                                <button-modal id="friends-list" title="Seguidos" :button="false">
                                                    <template slot="button">
                                                        <button class="btn-text">Gestionar seguidos</button>
                                                    </template>
                                                    <template slot="modal-body">
                                                        <ul class="profiles-list">
                                                            <li v-for="(friend, i) in user.current_twitter_profile[0].friends" :id="'element-' + friend.screen_name">
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
                                    <div class="row">
                                        <div class="col">
                                            <line-chart :data="friendsGraphData" width="100%"></line-chart>
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
                                                    <h4 class="card-title">Dejados de seguir recientemente</h4>
                                                </div>
                                                <div class="col-auto text-right">
                                                    <select class="form-control" @input="fetchUnfriendsGraphData($event.target.value)">
                                                        <option value="weekly" selected>7 días</option>
                                                        <option value="biweekly">14 días</option>
                                                        <option value="monthly">30 días</option>
                                                        <option value="yearly">1 año</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="unfriends-list-count-container">
                                                <span class="stat-amount">{{ user.current_twitter_profile[0].unfriends.length }}</span>
                                                <button-modal id="unfriends-list" title="Dejados de seguir" :button="false">
                                                    <template slot="button">
                                                        <button class="btn-text">Detalles</button>
                                                    </template>
                                                    <template slot="modal-body">
                                                        <ul class="profiles-list">
                                                            <li v-for="(unfriend, i) in user.current_twitter_profile[0].unfriends" :id="'element-' + unfriend.screen_name">
                                                                <div class="row no-gutters profile-link">
                                                                    <a :href="'https://twitter.com/' + unfriend.screen_name" class="col-auto">
                                                                        <img :src="unfriend.profile_image_url" :alt="'Foto de perfil de @' + unfriend.screen_name">
                                                                    </a>
                                                                    <div class="col">
                                                                        <span class="name">
                                                                            <a :href="'https://twitter.com/' + unfriend.screen_name">
                                                                                {{ unfriend.name }}
                                                                                <span v-if="unfriend.follows_you" class="badge badge-success">Te sigue</span>
                                                                                <span v-else class="badge badge-danger">No te sigue</span>
                                                                            </a>
                                                                        </span>
                                                                        <span class="screen-name text-muted">@{{ unfriend.screen_name }}</span>
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
                                    <span class="stat-amount">{{ user.current_twitter_profile[0].reports[user.current_twitter_profile[0].reports.length - 1].followers_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
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
                friendsGraphData: null,
                unfollowsData: null
            }
        },
        created() {
            this.fetchFriendsGraphData('weekly');
            this.fetchUnfriendsGraphData('weekly');
        },
        methods: {
            fetchFriendsGraphData(timeInterval) {
                axios.get('/ajax/profile/' + this.user.current_twitter_profile[0].id + '/stats/friends/' + timeInterval + '/')
                    .then((response) => {
                        this.friendsGraphData = response.data;
                    });
            },
            fetchUnfriendsGraphData(timeInterval) {
                axios.get('/ajax/profile/' + this.user.current_twitter_profile[0].id + '/stats/unfriends/' + timeInterval + '/')
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
