<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Estadísticas del perfil</h3>
                    </div>
                </div>

                <div class="row stat-list-container">
                    <div class="col">
                        <ul class="nav nav-tabs stat-list" id="myTab" role="tablist">
                            <li class="nav-item">
                                <button class="btn active" id="unfollower-stats-tab" data-toggle="tab" href="#unfollower-stats" role="tab" aria-controls="unfollower-stats" aria-selected="true">
                                    Seguidores
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="btn" id="friends-stats-tab" data-toggle="tab" href="#friends-stats" role="tab" aria-controls="friends-stats" aria-selected="false">
                                    Seguidos
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row no-gutters">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="row no-gutters profile-stats-container tab-pane fade show active" id="unfollower-stats" role="tabpanel" aria-labelledby="unfollower-stats-tab">
                                <div class="col-5">
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
                                                                            <select class="form-control" @input="getFollowersData($event.target.value)">
                                                                                <option value="weekly" selected>7 días</option>
                                                                                <option value="biweekly">14 días</option>
                                                                                <option value="monthly">30 días</option>
                                                                                <option value="yearly">1 año</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="followers-list-count-container">
                                                                        <span>{{ d_user.current_twitter_profile[0].followers.length }} seguidores</span>
                                                                        <a href="" data-toggle="modal" data-target="#followers-modal">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                    </div>

                                                                    <div class="modal fade" id="followers-modal" tabindex="-1" role="dialog" aria-labelledby="followers-modal-label" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="followers-modal-label">
                                                                                        Seguidores</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <ul class="profiles-list">
                                                                                        <li v-for="(follower, i) in d_user.current_twitter_profile[0].followers">
                                                                                            <div class="row no-gutters profile-link">
                                                                                                <a :href="'https://twitter.com/' + follower.screen_name" class="col-auto">
                                                                                                    <img :src="follower.profile_image_url" :alt="'Foto de perfil de @' + follower.screen_name">
                                                                                                </a>
                                                                                                <div class="col">
                                                                                                    <a :href="'https://twitter.com/' + follower.screen_name" class="name">{{
                                                                                                        follower.name
                                                                                                        }}</a>
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
                                                                                <option value="weekly" selected>7 días</option>
                                                                                <option value="biweekly">14 días</option>
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
                                <div class="col-3">
                                    <div class="row card-row">
                                        <div class="col">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="card-title">Porcentaje de follow-back</h4>
                                                    <div class="row card-content">
                                                        <div class="col">
                                                            <span class="stat-amount">{{ d_user.current_twitter_profile[0].reports[d_user.current_twitter_profile[0].reports.length - 1].followback_percent.toString().replace('.', ',') }}%</span>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button class="btn">Detalles</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row no-gutters profile-stats-container tab-pane fade" id="friends-stats" role="tabpanel" aria-labelledby="friends-stats-tab">
                                <h1>Friends</h1>
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
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .tab-content {
        .active {
            display: flex;
        }
    }

    .stat-list-container {
        padding-bottom: 1em;

        .stat-list {
            border: 0;

            li {
                &:not(:first-child) {
                    margin-left: 10px !important;
                }

                button {
                    padding: 6px 20px !important;
                    background-color: #e6e6e6 !important;
                    border: 1px solid #d7d7d7 !important;
                    color: #444 !important;
                    transition: 200ms !important;

                    &:hover {
                        background-color: #d7d7d7 !important;
                    }
                }
            }
        }
    }

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

                    #followers-modal, #unfollowers-modal {

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
                                    font-weight: bold !important;
                                    color: $textColor;
                                    line-height: initial;
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

    ::-webkit-scrollbar {
        width: 10px; /* Remove scrollbar space */
        padding-left: 50px;
        background-color: lighten(black, 85%);
        //background: transparent;  /* Optional: just make scrollbar invisible */
    }

    /* Optional: show position indicator in red */
    ::-webkit-scrollbar-thumb {
        background: lighten($primaryColor, 8%);
        margin-left: 10px;
    }

    .full-column {
        height: calc(100vh - 39.82px - 15px * 2 - 4px);
        overflow: scroll;
    }

    .half-column {
        height: calc((100vh - 39.82px - 15px * 2 - 4px) / 2);
        overflow: scroll;
        overflow-x: hidden;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;


    }
</style>
