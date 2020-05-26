<template>
    <div class="row">
        <div class="col-12 profile-stats-container">
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <graph-card id="followers" :user="user" :stat_endpoint="'/ajax/profile/' + user.current_synced_profile.id + '/reports/followers/'" card_title="Seguidores" modal_title="Seguidores">
                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>

                        <template slot="modal">
                            <ul class="profiles-list">
                                <li v-for="(follower, i) in user.current_synced_profile.followers" :id="'element-' + follower.screen_name">
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
                                        <div v-if="user.current_synced_profile.friends[follower.id_str]" class="col-4">
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
                    <graph-card id="follows" :user="user" :stat_endpoint="'/ajax/profile/' + user.current_synced_profile.id + '/reports/follows/'" card_title="Nuevos seguidores" modal_title="Nuevos seguidores">
                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="unfollows" :user="user" :stat_endpoint="'/ajax/profile/' + user.current_synced_profile.id + '/reports/unfollows/'" card_title="Unfollowers" modal_title="Unfollowers">
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
        ]
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
            }
        }

        &:not(:first-child) {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            div[class*="col"]:not(:first-child) {
                margin-top: 10px!important;
                padding-left: 0!important;
            }
        }
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
</style>
