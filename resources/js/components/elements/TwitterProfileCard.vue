<template>
    <div :class="colClass">
        <div class="card profile-card">
            <div class="banner-container">
                <img class="card-img-top" :src="twitterProfile.profile_banner_url" :alt="'Banner del perfil ' + twitterProfile.screen_name">
                <button @click="refreshProfile" class="btn btn-primary btn-refresh" data-toggle="tooltip" data-placement="top" title="Refrescar">
                    <i class="fa fa-sync"></i>
                </button>
            </div>

            <div class="profile-card-body">
                <div class="row">
                    <div class="col-auto">
                        <img class="profile-card-avatar" :src="twitterProfile.profile_image_url" :alt="'Avatar de ' + twitterProfile.screen_name">
                    </div>
                    <div class="col">
                        <div class="row">
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
            colClass: function() {
                return 'col-' + (this.colSize ? this.colSize : 4);
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
                    } else {
                        this.$toast.error(response.data.message);
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
            .card-img-top {
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
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
</style>
