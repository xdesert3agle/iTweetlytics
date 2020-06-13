<template>
    <div class="new-tweet-button-container">
        <button type="button" class="btn btn-primary btn-icon btn-tweet" data-toggle="modal" data-target="#new-tweet-modal">
            <i class="fab fa-twitter"></i>
            <span>Tweet</span>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="new-tweet-modal" tabindex="-1" role="dialog" aria-labelledby="label-new-tweet-modal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div v-if="!isScheduling" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="label-new-tweet-modal">Nuevo Tweet</h5>
                        <button type="button" id="close-new-tweet-modal" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row no-gutters">
                            <div class="col-2 user-profile-img-container">
                                <img :src="user.current_user_profile.twitter_profile.profile_image_url" class="user-profile-img" alt="Tu imagen de perfil">
                            </div>
                            <div class="col">
                                <textarea class="js-autoresize" v-model="newTweetText" maxlength="280" placeholder="¿Qué está pasando?"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span class="scheduled-date" v-if="scheduleTime">
                            <i class="fas fa-calendar-day"></i>
                            {{ formattedScheduleTime }}
                        </span>
                        <button @click="isScheduling = true" type="button" class="btn btn-info btn-round">Programar</button>
                        <button @click="sendTweet" type="button" class="btn btn-primary btn-round">{{ scheduleTime == null ? 'Twittear' : 'Enviar tweet programado' }}</button>
                    </div>
                </div>
                <div v-else class="modal-content">
                    <div class="modal-header">
                        <i @click="isScheduling = false" class="fa fa-lg fa-chevron-left"></i>
                        <h5 class="modal-title">Programar tweet</h5>
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row no-gutters">
                            <div class="col-12">
                                <h6>Fecha y hora a la que se publicará el tweet</h6>
                                <date-picker v-model="scheduleTime" type="datetime" valueType="timestamp"></date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="isScheduling = false" type="button" class="btn btn-primary btn-round">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {setResizeListeners} from "../../../../../helpers/auto-resize.js";
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        props: [
            'user'
        ],
        components: {
            DatePicker
        },
        data() {
            return {
                newTweetText: "",
                isScheduling: false,
                scheduleTime: null,
            }
        },
        computed: {
            formattedScheduleTime() {
                let a = new Date(this.scheduleTime);
                let months = ['Ene.', 'Feb.', 'Mar.', 'Abr.', 'May.', 'Jun.', 'Jul.', 'Ago.', 'Sep.', 'Oct.', 'Nov.', 'Dic.'];
                let year = a.getFullYear();
                let month = months[a.getMonth()];
                let date = a.getDate();
                let hour = a.getHours();
                let min = a.getMinutes() < 10 ? '0' + a.getMinutes() : a.getMinutes();
                let sec = a.getSeconds() < 10 ? '0' + a.getSeconds() : a.getSeconds();

                return date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec;
            }
        },
        mounted() {
            setResizeListeners(this.$el, ".js-autoresize");
        },
        methods: {
            sendTweet() {
                axios.post('/ajax/tweets/new', {
                    'user_profile_id': this.user.current_user_profile.id,
                    'text': this.newTweetText,
                    'scheduleTime': this.scheduleTime,
                    'now': Date.now()
                }).then((response) => {
                    let toastType;

                    if (response.data.status == 'success') {
                        toastType = 'success';
                        this.newTweetText = "";
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
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .btn-tweet {
        padding: 2px 10px!important;
    }

    .modal {
        .modal-header {
            border: none;

            i {
                padding: 9px;
                margin-left: -7px;
                cursor: pointer;
            }

            .label-new-tweet-modal {
                color: $textColor;
            }

            .close {
                margin: 0 10px 0 0 !important;
                padding: 0 !important;
                line-height: initial;
            }
        }

        .modal-body {
            .user-profile-img-container {
                padding-right: 15px;

                .user-profile-img {
                    width: 100%;
                    border-radius: 50%;
                }
            }

            textarea {
                width: 100%;
                height: 100px;
                border: none;
                font-size: 15pt;
                resize: none;
                color: rgb(33, 37, 41);

                &::placeholder {
                    font-size: 15pt;
                    font-weight: 500;
                    color: rgba(0, 0, 0, 0.3);
                }
            }
        }

        .modal-footer {
            display: block;
            text-align: right;

            border: none;

            .btn-info {
                color: #444;
                background-color: #e8e8e8;
                border: 0;

                &:hover {
                    background-color: darken(#e8e8e8, 7%);
                }
            }

            .scheduled-date {
                display: block!important;
                padding-right: 5px;

                font-weight: 500;
                text-align: right;
                color: $textColor;
            }
        }
    }
</style>
