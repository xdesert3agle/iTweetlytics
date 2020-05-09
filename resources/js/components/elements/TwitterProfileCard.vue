<template>
    <div :class="cardClasses">
        <div class="card profile-card">
            <div class="banner-container">
                <img class="card-img-top" :src="twitterProfile.profile_banner_url" :alt="'Banner del perfil ' + twitterProfile.screen_name">
                <div class="banner-content-container">
                    <button @click="refreshProfile" class="btn btn-primary btn-refresh" data-toggle="tooltip" data-placement="top" title="Actualizar perfil">
                        <i class="fa fa-sync"></i>
                    </button>

                    <div class="row no-gutters">
                        <div class="col offset-3">
                            <span class="profile-name">{{ twitterProfile.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card-body">
                <div class="row no-gutters">
                    <div class="col-md-3 col-3 avatar-col">
                        <img class="profile-card-avatar" :src="twitterProfile.profile_image_url" :alt="'Avatar de ' + twitterProfile.screen_name">
                    </div>
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col profile-card-attribute">
                                <span>{{ twitterProfile.statuses_count }}</span>
                                <h5 class="text-muted">Tweets</h5>
                            </div>
                            <div class="col profile-card-attribute">
                                <span>{{ twitterProfile.friends_count }}</span>
                                <h5 class="text-muted">Siguiendo</h5>
                            </div>
                            <div class="col profile-card-attribute">
                                <span>{{ twitterProfile.followers_count }}</span>
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
            twitterProfile: Object,
            colSize: Number
        },
        mounted() {
            this.activateTooltips();
        },
        computed: {
            cardClasses: function() {
                return 'col-md-' + (this.colSize ? this.colSize : 4) + ' col-12 twitter-profile-card';
            },
            refreshProfileUrl() {
                return '/ajax/user/refresh/' + this.twitterProfile.id;
            }
        },
        methods: {
            refreshProfile: function() {
                axios.get(this.refreshProfileUrl).then((response) => {
                    if (response.data.status == 'success') {
                        this.twitterProfile = response.data.data;
                        this.$toastr.Add({
                            msg: response.data.message, // Toast Message
                            clickClose: true, // Click Close Disable
                            timeout: 4000, // Remember defaultTimeout is 5 sec.(5000) in this case the toast won't close automatically
                            //progressBarValue: 50, // Manually update progress bar value later; null (not 0) is default
                            type: "success", // Toast type,
                            preventDuplicates: true, //Default is false,
                            classNames: ["animated", "slideInRight", "ms-300"],
                        });
                    } else {
                        this.$toastr.Add({
                            msg: response.data.message, // Toast Message
                            clickClose: true, // Click Close Disable
                            timeout: 4000, // Remember defaultTimeout is 5 sec.(5000) in this case the toast won't close automatically
                            type: "error", // Toast type,
                            preventDuplicates: true, //Default is false,
                            classNames: ["animated", "slideInRight", "ms-300"],
                        });
                    }
                });
            },
            activateTooltips: function() {
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

                .btn-refresh {
                    position: absolute;
                    top: 15px;
                    right: 15px;

                    width: 40px;
                    height: 40px;

                    display: flex;
                    justify-content: center;
                    border-radius: 50%!important;
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
                margin-right: 15px!important;
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

    @media (max-width: 576px) {
        .twitter-profile-card:not(:first-child) {
            margin-top: 15px;
        }
    }
</style>
