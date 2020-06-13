<template>
    <div class="col-md-3 col-12 twitter-profile-card">
        <div class="card profile-card">
            <div class="banner-container">
                <img class="card-img-top" :src="userProfile.twitter_profile.profile_banner_url" :alt="'Banner del perfil ' + userProfile.twitter_profile.screen_name">
                <div class="banner-content-container">
                    <div class="card-buttons-container">
                        <button @click="deleteProfile
" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar perfil">
                            <i class="fa fa-lg fa-times"></i>
                        </button>
                        <button :disabled="isThisTheActiveProfile" @click="changeProfile(userProfile.id)" class="btn btn-primary" data-toggle="tooltip" data-placement="top"
                                title="Acceder a este perfil">
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
            refreshProfileUrl() {
                return '/ajax/user/refresh/' + this.userProfile.twitter_profile.id;
            },
            isThisTheActiveProfile() {
                return this.userProfile.id == this.user.selected_profile;
            },
            isMobile() {
                let check = false;
                (function (a) {
                    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
                })(navigator.userAgent || navigator.vendor || window.opera);
                return check;
            },
            isLaptop() {
                navigator.getBattery().then(function (battery) {
                    if (battery.charging && battery.chargingTime === 0) {
                        return false;
                    } else {
                        return true;
                    }
                });
            }
        },
        methods: {
            deleteProfile() {
                axios.get(this.refreshProfileUrl).then((response) => {
                    let toastType;

                    if (response.data.status == 'success') {
                        toastType = 'success';
                        this.userProfile = response.data.data;
                    } else {
                        toastType = 'error';
                    }

                    this.$toastr.Add({
                        msg: response.data.message,
                        clickClose: true,
                        timeout: 4000,
                        type: toastType,
                        preventDuplicates: true,
                        classNames: ["animated", "slideInRight", "ms-300"],
                    });
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
