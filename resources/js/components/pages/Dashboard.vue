<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <ul class="dash-menu">
                    <li>
                        <a href="/dashboard/profiles">Mis perfiles</a>
                    </li>
                    <li>
                        <form action="/logout" method="POST">
                            <csrf></csrf>
                            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col">
                <div v-if="user.twitter_profiles.length > 0" class="dash-content">
                    <div class="row">
                        <div class="col">
                            <form action="/twitter/login">
                                <button type="submit" class="btn btn-primary btn-add-tw-profile">Añadir un perfil de Twitter <i class="fab fa-twitter"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4" v-for="(profile, i) in user.twitter_profiles">
                            <div class="card profile-card">
                                <img class="card-img-top" :src="profile.profile_banner_url" :alt="'Banner del perfil ' + profile.screen_name">

                                <div class="profile-card-body">
                                    <div class="row">
                                        <div class="col-auto">
                                            <img class="profile-card-avatar" :src="profile.profile_image_url" :alt="'Avatar de ' + profile.screen_name">
                                        </div>
                                        <div class="col">
                                            <div class="row">
                                                <div class="col profile-card-attribute">
                                                    <span>{{ profile.statuses_count }}</span>
                                                    <h5 class="text-muted">Tweets</h5>
                                                </div>
                                                <div class="col profile-card-attribute">
                                                    <span>{{ profile.friends_count }}</span>
                                                    <h5 class="text-muted">Siguiendo</h5>
                                                </div>
                                                <div class="col profile-card-attribute">
                                                    <span>{{ profile.followers_count }}</span>
                                                    <h5 class="text-muted">Seguidores</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="first-profile-container">
                    <p>No has agregado ningún perfil de Twitter a tu cuenta de iTweetlytics. Agrega uno para comenzar</p>
                    <div class="row">
                        <div class="col">
                            <form action="/twitter/login">
                                <button type="submit" class="btn btn-primary">Añadir perfil de Twitter <i class="fab fa-twitter"></i></button>
                            </form>
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
        computed: {
            hasUserLinkedAProfile() {
                return twitter_profiles;
            },
            currentYear() {
                return new Date().getFullYear();
            },
        },
    }
</script>

<style lang="scss" scoped>
    .profile-card {
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        border: none;

        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .profile-card-body {
            padding: 15px;

            .profile-card-avatar {
                width: 110px;
                border-radius: 50%;
                margin-top: -90px;
            }

            .profile-card-attribute {
                h5 {
                    margin: 0;
                    text-transform: uppercase;
                    font-size: 10pt;
                    font-weight: 500;
                }

                span {
                    font-size: 15pt;
                    font-weight: 500;
                    line-height: initial;
                }
            }
        }
    }

    .btn-add-tw-profile {
        margin-bottom: 1em;
    }

    .dash-menu {
        list-style-type: none;
        padding: 0;

        li {
            padding: 10px 0;
            text-transform: uppercase;
            font-size: 11pt;

            &:last-child {
                color: red!important;
            }
        }
    }
</style>
