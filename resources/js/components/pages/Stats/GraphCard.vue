<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ card_title }}{{ stat.is_accumulated ? ' en ' + timeIntervalString
                                : "" }}</h4>
                        </div>
                        <div class="col-auto text-right">
                            <select class="form-control" @input="timeIntervalChanged">
                                <option value="yesterday">1 día</option>
                                <option value="weekly" selected>7 días</option>
                                <option value="monthly">30 días</option>
                                <option value="yearly">1 año</option>
                            </select>
                        </div>
                    </div>
                    <div class="stat-container">
                        <div class="stat-wrapper">
                            <span class="stat-amount">{{ stat.value }}</span>

                            <div v-if="!stat.is_accumulated" class="stat-variation-container" :class="{'increase': stat.variation > 0, 'decrease': stat.variation < 0, 'no-variation': stat.variation == 0}">
                                <i v-if="stat.variation > 0" class="fa fa-lg fa-caret-up"></i>
                                <i v-else-if="stat.variation === 0" class="fa fa-sm fa-equals"></i>
                                <i v-else-if="stat.variation < 0" class="fa fa-lg fa-caret-down"></i>
                                <span class="variation-amount">
                                    {{ stat.variation !== 0 ? Math.abs(stat.variation) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="modal_title" class="row no-gutters modal-trigger-row">
                        <div class="col">
                            <button-modal :id="id" :title="modal_title" :button="false">
                                <template slot="button">
                                    <slot name="modal-trigger"></slot>
                                </template>
                                <template slot="modal-body">
                                    <ul v-if="profilesList && profilesList.length > 0" class="profiles-list">
                                        <li v-for="(profile, i) in profilesList" :id="'element-' + profile.twitter_profile.screen_name">
                                            <div class="row no-gutters profile-link">
                                                <a :href="'https://twitter.com/' + profile.twitter_profile.screen_name" class="col-auto">
                                                    <img :src="profile.twitter_profile.profile_image_url" :alt="'Foto de perfil de @' + profile.twitter_profile.screen_name">
                                                </a>
                                                <div class="col">
                                                    <span class="name">
                                                        <a :href="'https://twitter.com/' + profile.twitter_profile.screen_name">
                                                            {{ profile.twitter_profile.name }}
                                                        </a>
                                                        <span @click.prevent v-if="(shouldShowFollowingStat && profile.is_following_back) || endpointIncludes('/followers/')" class="badge badge-success">Te sigue</span>
                                                        <span @click.prevent v-else class="badge badge-danger">No te sigue</span>
                                                    </span>
                                                    <span class="screen-name text-muted">@{{ profile.twitter_profile.screen_name }}</span>
                                                </div>
                                                <div v-if="profile.is_followed_back || endpointIncludes('/friends/')" class="col-4">
                                                    <button @click="unfollowUser(profile.twitter_profile.screen_name, i)" class="btn btn-sm btn-unfollow">
                                                        Dejar de seguir
                                                    </button>
                                                </div>
                                                <div v-else class="col-4">
                                                    <button @click="followUser(profile.twitter_profile.screen_name, i)" class="btn btn-sm btn-follow">
                                                        Seguir
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <span v-else>No hay información disponible.</span>
                                </template>
                            </button-modal>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <line-chart :data="graphData" width="100%" height="215px"></line-chart>
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
            'id',
            'card_title',
            'stat_endpoint',
            'modal_title',
            'user'
        ],
        data() {
            return {
                graphData: null,
                stat: {
                    value: null,
                    variation: null,
                    is_accumulated: null
                },
                profilesList: null,
                timeInterval: 'weekly',
                d_user: this.user,
            }
        },
        computed: {
            timeIntervalString() {
                switch (this.timeInterval) {
                    case 'yesterday':
                        return "el último día";
                    case 'weekly':
                        return "los últimos 7 días";
                    case 'monthly':
                        return "los últimos 30 días";
                    case 'yearly':
                        return "el último año";
                }
            },
            shouldShowFollowingStat() {
                return this.stat_endpoint.toLowerCase().includes('friend');
            },
            shouldShowFollowerStat() {
                return this.stat_endpoint.toLowerCase().includes('follow');
            },
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                axios.get(this.stat_endpoint + "/" + this.timeInterval + "/")
                    .then((response) => {
                        this.stat = response.data.stat;
                        this.graphData = response.data.graph;
                        this.profilesList = response.data.users_list;
                    });
            },
            timeIntervalChanged($event) {
                this.timeInterval = $event.target.value;
                this.fetchData();
            },
            unfollowUser(screen_name, index) {
                axios.post('/ajax/profile/unfollow', {
                    'screen_name': screen_name,
                    'user_profile_id': this.d_user.current_user_profile.id
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
                    'user_profile_id': this.d_user.current_user_profile.id
                }).then((response) => {
                    if (response.data.status == 'success') {
                        this.twitterProfile = response.data.data;
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: true,
                            timeout: 4000,
                            type: 'success',
                            preventDuplicates: true,
                            classNames: ['animated', 'slideInRight', 'ms-300'],
                        });
                        this.d_user.current_user_profile.friends.splice(index, 1);
                        $('#element-' + screen_name).remove();
                    } else {
                        this.$toastr.Add({
                            msg: response.data.message, // Toast Message
                            clickClose: true,
                            timeout: 4000,
                            type: 'error',
                            preventDuplicates: true,
                            classNames: ['animated', 'slideInRight', 'ms-300'],
                        });
                    }
                });
            },
            endpointIncludes($needle) {
                return this.stat_endpoint.toLowerCase().includes($needle);
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;
    .card {
        height: 400px;

        .card-body {
            display: flex;
            flex-direction: column;
            max-height: 100vh;
            overflow: hidden;

            > .row {
                height: 100%;

                .col {
                    display: flex;
                    flex-direction: column;
                    height: 100%;

                    .profiles-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;

                        li {
                            &:not(:first-child) {
                                margin-top: 15px;
                            }

                            .profile-link {
                                align-items: center;

                                div[class*="col"] {
                                    &:not(:first-child) {
                                        margin-left: 15px;
                                        margin-bottom: 7px;
                                    }
                                }

                                a {
                                    text-decoration: none;
                                }

                                img {
                                    border-radius: 50%;
                                    width: 65px;
                                    height: 65px;
                                    object-fit: cover;
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

                    .stat-container {
                        display: flex;
                        align-items: flex-start;
                        flex: 1;

                        .stat-wrapper {
                            display: flex;
                            align-items: center;

                            .stat-amount {
                                color: $primaryColor;
                                line-height: initial;
                                font-size: 32pt;
                                font-weight: bold;
                            }

                            .stat-variation-container {
                                display: flex;
                                align-items: center;
                                flex-direction: column;
                                margin-left: 5px;

                                i {
                                    font-size: 23pt;
                                    line-height: 16pt;

                                    &.fa-equals {
                                        font-size: 18pt !important;
                                        margin-left: 4px;
                                    }
                                }

                                .variation-amount {
                                    line-height: initial;
                                    font-size: 16pt;
                                    font-weight: 500;
                                }

                                &.no-variation {
                                    color: rgba(40, 41, 41, 0.96);
                                }

                                &.increase {
                                    color: #4BB543;
                                }

                                &.decrease {
                                    color: #F2262D;
                                }
                            }
                        }
                    }
                }
            }

            .card-title {
                font-size: 16pt;
            }

            .card-content {
                align-items: center;
            }

            .followers-list-container, .unfollowers-list-container {
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

            .modal-trigger-row {
                margin: 10px 0 15px 0;
            }
        }
    }
</style>
