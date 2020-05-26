<template>
    <div class="row">
        <div class="col-12">
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <graph-card id="followers" :user="user" :stat_endpoint="'/ajax/profile/' + d_user.current_synced_profile.id + '/reports/f2f_ratio/'" card_title="Ratio seguidores/seguidos"></graph-card>
                </div>
                <div class="col-md-4 col-12 small-cards-container">
                    <div class="row no-gutters">
                        <div class="col-md-6 col-12">
                            <div class="card small-card">
                                <div class="card-body">
                                    <h4 class="card-title">Seguidos que te siguen de vuelta</h4>
                                    <span class="stat-amount">{{ d_user.current_synced_profile.reports[d_user.current_synced_profile.reports.length - 1].followers_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card small-card">
                                <div class="card-body">
                                    <h4 class="card-title">Seguidores que sigues de vuelta</h4>
                                    <span class="stat-amount">{{ d_user.current_synced_profile.reports[d_user.current_synced_profile.reports.length - 1].user_followback_percent.toFixed(2).toString().replace('.', ',') }}%</span>
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
                    'synced_profile_id': this.d_user.current_synced_profile.id
                }).then((response) => {
                    if (response.data.status == 'success') {
                        this.twitterProfile = response.data.data;
                        this.$toast.success(response.data.message);
                    } else {
                        this.$toast.error(response.data.message);
                    }
                });
            },
            followUser(screen_name, index) {
                axios.post('/ajax/profile/follow', {
                    'screen_name': screen_name,
                    'synced_profile_id': this.d_user.current_synced_profile.id
                }).then((response) => {
                    if (response.data.status == 'success') {
                        this.twitterProfile = response.data.data;
                        this.$toast.success(response.data.message);

                        this.d_user.current_synced_profile.friends.splice(index, 1);
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

    .card-row {
        div[class*="col"] {
            display: flex;
            flex-direction: column;

            &:not(:first-child) {
                padding-left: 10px;

                @media (max-width: 768px) {
                    padding-left: 0 !important;
                    margin-top: 10px !important;
                }
            }

            @media (max-width: 768px) {
                &:not(.small-cards-container):not(:first-child) {
                    margin-top: 10px !important;
                }
            }
        }
    }

    .small-cards-container {
        .row:not(:first-child) {
            margin-top: 10px;

            @media (max-width: 768px) {
                margin-top: 0 !important;
            }
        }

        @media (max-width: 768px) {
            margin-top: 0 !important;
            padding-top: 0 !important;

            .row:not(:first-child) {
                margin-top: 10px !important;
            }
        }
    }

    .card-row {
        //flex: 1;

        .card {
            height: 400px;

            &.small-card {
                height: 195px;
            }

            .card-body {
                display: flex;
                flex-direction: column;

                .row {
                    height: 100%;
                }

                .card-title {
                    font-size: 16pt;
                }
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

                button {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 95%;

                    padding: 7px 0 !important;

                    background: transparent;

                    text-transform: uppercase;

                    &.btn-follow {
                        color: $primaryColor;
                        border-color: $primaryColor;
                        transition: 150ms;

                        &:hover {
                            background-color: $primaryColor;
                            color: white;
                        }
                    }

                    &.btn-unfollow {
                        color: #c80000;
                        border-color: #c80000;
                        transition: 150ms;

                        &:hover {
                            background-color: #c80000;
                            color: white;
                        }
                    }

                    i {
                        margin-right: 7px;
                        margin-bottom: 1px;
                    }
                }
            }
        }

        &:not(:first-child) {
            margin-top: 10px;
        }
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
</style>
