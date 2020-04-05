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
                                <button type="submit" class="btn btn-primary btn-icon btn-add-tw-profile">Añadir un perfil de Twitter <i class="fab fa-twitter"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <twitter-profile-card v-for="(tw_profile, i) in user.twitter_profiles" :twitterProfile="tw_profile" :colSize="4"></twitter-profile-card>
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
            'user',
        ],
        computed: {
            hasUserLinkedAProfile() {
                return this.user.twitter_profiles;
            },
            currentYear() {
                return new Date().getFullYear();
            },
        },
    }
</script>

<style lang="scss" scoped>

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
