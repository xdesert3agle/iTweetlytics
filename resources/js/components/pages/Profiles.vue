<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div v-if="user.user_profiles != null" class="dash-content">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Perfiles sincronizados <small>- {{ user.user_profiles.length }} perfiles</small></h3>
                        </div>
                    </div>
                    <div class="row">
                        <twitter-profile-card v-for="(user_profile, i) in user.user_profiles" :key="user_profile.id" :user="user" :userProfile="user_profile"></twitter-profile-card>
                    </div>
                </div>
                <div v-else class="first-profile-container">
                    <h5>No has agriegado ningún perfil de Twitter a tu cuenta de iTweetlytics. Agrega uno para comenzar</h5>
                </div>

                <div class="row">
                    <div class="col">
                        <form action="/twitter/login">
                            <button type="submit" class="btn btn-primary">
                                Añadir un nuevo perfil de Twitter
                            </button>
                        </form>
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
                return this.user.user_profiles;
            },
            currentYear() {
                return new Date().getFullYear();
            },
        }
    }
</script>

<style lang="scss" scoped>

</style>
