<template>
    <div class="col-md-3 col-12 twitter-profile-card">
        <div class="card profile-card">
            <div class="banner-container">
                <img class="card-img-top" :src="userProfile.twitter_profile.profile_banner_url" :alt="'Banner del perfil ' + userProfile.twitter_profile.screen_name">
                <div class="banner-content-container">
                    <div class="card-buttons-container">
                        <button @click="deleteProfile" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar perfil">
                            <i class="fa fa-lg fa-times"></i>
                        </button>
                        <button :disabled="isThisTheActiveProfile" @click="changeProfile(userProfile.id)" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Acceder a este perfil">
                            <i class="fas fa-door-open"></i>
                        </button>
                    </div>
                    <div class="row no-gutters">
                        <div class="col offset-3">
                            <span class="profile-name">{{ userProfile.twitter_profile.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card-body">
                <div class="row no-gutters">
                    <div class="col-md-3 col-3 avatar-col">
                        <img class="profile-card-avatar" :src="userProfile.twitter_profile.profile_image_url" :alt="'Avatar de ' + userProfile.twitter_profile.screen_name">
                    </div>
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col profile-card-attribute">
                                <span>{{ userProfile.twitter_profile.statuses_count }}</span>
                                <h5 class="text-muted">Tweets</h5>
                            </div>
                            <div class="col profile-card-attribute">
                                <span>{{ userProfile.twitter_profile.friends_count }}</span>
                                <h5 class="text-muted">Siguiendo</h5>
                            </div>
                            <div class="col profile-card-attribute">
                                <span>{{ userProfile.twitter_profile.followers_count }}</span>
                                <h5 class="text-muted">Seguidores</h5>
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
        props: {
            user: Object,
            userProfile: Object,
            selectedProfile: String,
        },
        mounted() {
            this.activateTooltips();
        },
        computed: {
            unsyncProfileUrl() {
                return '/ajax/profile/unsync';
            },
            isThisTheActiveProfile() {
                return this.userProfile.id == this.user.selected_profile;
            },
        },
        methods: {
            deleteProfile() {
                axios.post(this.unsyncProfileUrl, {
                    'user_profile_id': this.userProfile.id
                }).then((response) => {
                    if (response.data.status == 'success') {
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: true,
                            timeout: 2500,
                            type: 'success',
                            preventDuplicates: true,
                            classNames: ["animated", "slideInRight", "ms-300"],
                            onClosed: () => window.location.reload()
                        });
                    } else {
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: true,
                            timeout: 4000,
                            type: 'error',
                            preventDuplicates: true,
                            classNames: ["animated", "slideInRight", "ms-300"],
                        });
                    }
                });
            },
            changeProfile(id) {
                axios.post('/ajax/user/profile/change', {
                    'target_profile': id
                }).then((response) => {
                    let toastType;

                    if (response.data.status == 'success') {
                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: true,
                            timeout: 1500,
                            type: response.data.status,
                            preventDuplicates: true,
                            classNames: ["animated", "slideInRight", "ms-300"],
                            onClosed: () => window.location.reload()
                        });
                    } else {
                        toastType = 'error';

                        this.$toastr.Add({
                            msg: response.data.message,
                            clickClose: true,
                            timeout: 3000,
                            type: response.data.status,
                            preventDuplicates: true,
                            classNames: ["animated", "slideInRight", "ms-300"],
                        });
                    }
                });
            },
            activateTooltips() {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .profile-card {
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        border: none;

        .banner-container {
            position: relative;

            .card-img-top {
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            .banner-content-container {
                position: absolute;
                top: 0;
                width: 100%;
                height: 100%;
                padding-left: 15px;

                display: flex;
                align-items: flex-end;
                justify-content: center;

                box-shadow: inset 0px -60px 50px -30px rgba(0, 0, 0, 0.5);
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;

                .row {
                    width: 100%;
                    padding: 10px 0;
                }

                .card-buttons-container {
                    position: absolute;
                    top: 15px;
                    right: 15px;

                    button {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        border-radius: 50% !important;
                        width: 40px;
                        height: 40px;

                        &:not(:first-child) {
                            margin-top: 15px;
                        }

                        &:disabled {
                            background: #919597;
                            opacity: 1;
                            cursor: not-allowed;
                        }
                    }
                }

                .profile-name {
                    line-height: initial;
                    font-size: 16pt;
                    color: white;
                    margin-left: 12px;
                }
            }
        }

        .profile-card-body {
            padding: 15px;

            .avatar-col {
                margin-right: 15px !important;
            }

            .profile-card-avatar {
                width: 100%;
                border-radius: 50%;
                margin-top: -90px;
                margin-right: 20px;
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

    @media (max-width: 1280px) {
        .twitter-profile-card {
            flex: 0 0 33% !important;
            max-width: 33% !important;
        }
    }

    @media (max-width: 576px) {
        .twitter-profile-card {
            flex: 0 0 100% !important;
            max-width: 100% !important;

            &:not(:first-child) {
                margin-top: 15px;
            }
        }
    }
</style>
