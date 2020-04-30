<template>
    <div class="row">
        <div class="col-12">
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <graph-card id="friends"
                                :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/friends/'"
                                card_title="Seguidos"
                                modal_title="Seguidos">

                        <template slot="modal-trigger">
                            <button class="btn-text">Gestionar seguidos</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(friend, i) in d_user.current_twitter_profile[0].friends" :id="'element-' + friend.screen_name">
                                    <div class="row profile-link">
                                        <a :href="'https://twitter.com/' + friend.screen_name" class="col-auto">
                                            <img :src="friend.profile_image_url" :alt="'Foto de perfil de @' + friend.screen_name">
                                        </a>
                                        <div class="col">
                                            <span class="name">
                                                <a :href="'https://twitter.com/' + friend.screen_name">
                                                    {{ friend.name }}
                                                </a>
                                                <span v-if="d_user.current_twitter_profile[0].followers[friend.id_str]" class="badge badge-success">Te sigue</span>
                                                <span v-else class="badge badge-danger">No te sigue</span>
                                            </span>
                                            <span class="screen-name text-muted">@{{ friend.screen_name }}</span>
                                        </div>
                                        <div v-if="d_user.current_twitter_profile[0].friends[friend.id_str]" class="col-4">
                                            <button @click="unfollowUser(friend.screen_name, i)" class="btn btn-sm btn-unfollow">
                                                <i class="fa fa-xs fa-times"></i>
                                                Dejar de seguir
                                            </button>
                                        </div>
                                        <div v-else class="col-4">
                                            <button @click="followUser(friend.screen_name, i)" class="btn btn-sm btn-follow">
                                                <i class="fa fa-xs fa-plus"></i>
                                                Empezar a seguir
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="befriends"
                                :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/befriends/'"
                                card_title="Seguidos"
                                modal_title="Seguidos">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(befriend, i) in d_user.current_twitter_profile[0].befriends" :id="'element-' + befriend.screen_name">
                                    <div class="row profile-link">
                                        <a :href="'https://twitter.com/' + befriend.screen_name" class="col-auto">
                                            <img :src="befriend.profile_image_url" :alt="'Foto de perfil de @' + befriend.screen_name">
                                        </a>
                                        <div class="col">
                                            <span class="name">
                                                <a :href="'https://twitter.com/' + befriend.screen_name">
                                                    {{ befriend.name }}
                                                </a>

                                                <span v-if="d_user.current_twitter_profile[0].followers[befriend.id_str]" class="badge badge-success">Te sigue</span>
                                                <span v-else class="badge badge-danger">No te sigue</span>
                                            </span>
                                            <span class="screen-name text-muted">@{{ befriend.screen_name }}</span>
                                        </div>
                                        <div v-if="d_user.current_twitter_profile[0].friends[befriend.id_str]" class="col-4">
                                            <button @click="unfollowUser(befriend.screen_name, i)" class="btn btn-sm btn-unfollow">
                                                <i class="fa fa-xs fa-times"></i>
                                                Dejar de seguir
                                            </button>
                                        </div>
                                        <div v-else class="col-4">
                                            <button @click="followUser(befriend.screen_name, i)" class="btn btn-sm btn-follow">
                                                <i class="fa fa-xs fa-plus"></i>
                                                Empezar a seguir
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="unfriends"
                                :stat_endpoint="'/ajax/profile/' + d_user.current_twitter_profile[0].id + '/reports/unfriends/'"
                                card_title="Dejados de seguir"
                                modal_title="Dejados de seguir">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(unfriend, i) in d_user.current_twitter_profile[0].unfriends" :id="'element-' + unfriend.screen_name">
                                    <div class="row profile-link">
                                        <a :href="'https://twitter.com/' + unfriend.screen_name" class="col-auto">
                                            <img :src="unfriend.profile_image_url" :alt="'Foto de perfil de @' + unfriend.screen_name">
                                        </a>
                                        <div class="col">
                                            <span class="name">
                                                <a :href="'https://twitter.com/' + unfriend.screen_name">
                                                    {{ unfriend.name }}
                                                </a>

                                                <span v-if="d_user.current_twitter_profile[0].followers[unfriend.id_str]" class="badge badge-success">Te sigue</span>
                                                <span v-else class="badge badge-danger">No te sigue</span>
                                            </span>
                                            <span class="screen-name text-muted">@{{ unfriend.screen_name }}</span>
                                        </div>
                                        <div v-if="d_user.current_twitter_profile[0].friends[unfriend.id_str]" class="col-4">
                                            <button @click="unfollowUser(unfriend.screen_name, i)" class="btn btn-sm btn-unfollow">
                                                <i class="fa fa-xs fa-times"></i>
                                                Dejar de seguir
                                            </button>
                                        </div>
                                        <div v-else class="col-4">
                                            <button @click="followUser(unfriend.screen_name, i)" class="btn btn-sm btn-follow">
                                                <i class="fa fa-xs fa-plus"></i>
                                                Empezar a seguir
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                    </graph-card>
                </div>
            </div>
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Follow-back a tus seguidores</h4>
                            <div class="row">
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
            .card-body {
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

                    padding: 7px 0!important;

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
