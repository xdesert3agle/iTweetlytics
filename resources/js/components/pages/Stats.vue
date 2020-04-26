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
                                <button class="btn active" id="follower-stats-tab" data-toggle="tab"
                                        href="#follower-stats"
                                        role="tab" aria-controls="follower-stats" aria-selected="true">Seguidores
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="btn" id="friends-stats-tab" data-toggle="tab" href="#friends-stats"
                                        role="tab" aria-controls="friends-stats" aria-selected="false">Seguidos
                                </button>
                            </li>
                            <li class="nav-item">
                                <select class="form-control" v-model="timeInterval"
                                        @input="changeTimeInterval($event.target.value)">
                                    <option value="weekly">7 días</option>
                                    <option value="biweekly">14 días</option>
                                    <option value="monthly">30 días</option>
                                    <option value="yearly">1 año</option>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row no-gutters">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="row no-gutters profile-stats-container tab-pane fade show active"
                                 id="follower-stats"
                                 role="tabpanel" aria-labelledby="follower-stats-tab">
                                <div class="col-auto">
                                    <div class="row card-row">
                                        <div class="col">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="card-title">Tus followers</h4>
                                                    <ul class="profiles-list">
                                                        <li v-for="(follower, i) in d_user.current_twitter_profile[0].followers">
                                                            <a :href="'https://twitter.com/' + follower.screen_name"
                                                               class="profile-link">
                                                                <span class="name">{{ follower.name }}</span>
                                                                <span
                                                                    class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row card-row">
                                        <div class="col">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h4 class="card-title">Followers</h4>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <line-chart
                                                                        :data="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/stats/followers/weekly/'"
                                                                        :discrete="true" width="100%"></line-chart>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h4 class="card-title">Followers recientes</h4>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col recent-followers-container">
                                                                    <ul>
                                                                        <li v-for="(follower, i) in d_user.current_twitter_profile[0].follows">
                                                                            <a :href="'https://twitter.com/' + follower.screen_name"
                                                                               class="profile-link">
                                                                                <span
                                                                                    class="name">{{ follower.name }}</span>
                                                                                <span
                                                                                    class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
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
                                                                    <h4 class="card-title">Unfollowers</h4>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <line-chart
                                                                        :data="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/stats/unfollows/weekly/'"
                                                                        :discrete="true" width="100%"></line-chart>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h4 class="card-title">Unfollowers recientes</h4>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div v-if="d_user.current_twitter_profile[0].unfollows.length > 0" class="col recent-followers-container">
                                                                    <ul>
                                                                        <li v-for="(unfollower, i) in d_user.current_twitter_profile[0].unfollows">
                                                                            <a :href="'https://twitter.com/' + unfollower.screen_name"
                                                                               class="profile-link">
                                                                                <span
                                                                                    class="name">{{ unfollower.name }}</span>
                                                                                <span
                                                                                    class="screen-name text-muted">@{{ unfollower.screen_name }}</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div v-else class="col">
                                                                    <span>No se ha encontrado ningún unfollow reciente.</span>
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
                                                    <span class="stat-amount">{{ d_user.current_twitter_profile[0].reports[d_user.current_twitter_profile[0].reports.length - 1].followback_percent.toString().replace('.', ',') }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row no-gutters profile-stats-container tab-pane fade" id="friends-stats"
                                 role="tabpanel"
                                 aria-labelledby="friends-stats-tab">
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
            }
        },
        methods: {
            changeTimeInterval(timeInterval) {
                axios.get('/ajax/user/get', {
                    params: {
                        timeInterval: timeInterval,
                        profileIndex: this.d_user.profile_index
                    }
                }).then((response) => {
                    console.log(response.data);
                    this.d_user = response.data;
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

                    .recent-followers-container {
                        max-height: 300px;
                        overflow-y: scroll;
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
                }
            }

            &:not(:first-child) {
                margin-top: 10px;
            }
        }
    }

    ::-webkit-scrollbar {
        width: 5px; /* Remove scrollbar space */
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

    .profile-link {
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
</style>
