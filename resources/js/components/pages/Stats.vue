<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3>Estadísticas del perfil</h3>

                <div class="row no-gutters stats-row">
                    <div class="col-auto">
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>Todos tus followers</h4>
                                        <ul class="full-column">
                                            <li v-for="(follower, i) in user.current_twitter_profile[0].followers">
                                                <a :href="'https://twitter.com/' + follower.screen_name" class="profile-link">
                                                    <span class="name">{{ follower.name }}</span>
                                                    <span class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row card-row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h4>Nuevos followers</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <line-chart :data="'/ajax/profile/' + user.current_twitter_profile[0].id + '/stats/followers/weekly/'" :discrete="true"></line-chart>
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
                                                <h4>Unfollowers</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <line-chart :data="'/ajax/profile/' + user.current_twitter_profile[0].id + '/stats/unfollows/weekly/'" :discrete="true"></line-chart>
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
                                        <div class="row">
                                            <div class="col">
                                                <h4>Porcentaje de follow-back</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <h3>90%</h3>
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
    </div>
</template>

<script>
    import {GChart} from 'vue-google-charts'

    export default {
        props: [
            'user'
        ],
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    ::-webkit-scrollbar {
        width: 5px;  /* Remove scrollbar space */
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
    }

    .stats-row {
        div[class*="col"] {
            display: flex;
            flex-direction: column;

            .card {
                height: 100%;
            }

            &:not(:first-child) {
                margin-left: 10px;
            }
        }

        .card-row {
            height: 100%;

            &:not(:first-child) {
                margin-top: 10px;
            }
        }
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
