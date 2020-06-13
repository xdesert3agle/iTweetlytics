<template>
    <div class="container-fluid app-container">
        <div class="row no-gutters app-wrapper">
            <aside id="sidebar" class="col-md-auto col-12">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li v-if="user.user_profiles != null" class="nav-item">
                        <a :class="{'active': user.user_profiles != null}" class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                            <i class="fas fa-lg fa-columns"></i>
                        </a>
                    </li>
                    <li v-if="user.user_profiles != null" class="nav-item">
                        <a class="nav-link" id="pills-stats-tab" data-toggle="pill" href="#pills-stats" role="tab" aria-controls="pills-home" aria-selected="false">
                            <i class="fas fa-lg fa-chart-line"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-scheduled-tweets-tab" data-toggle="pill" href="#pills-scheduled-tweets" role="tab" aria-controls="pills-scheduled-tweets" aria-selected="false">
                            <i class="fas fa-lg fa-calendar-alt"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-settings-tab" data-toggle="pill" href="#pills-settings" role="tab" aria-controls="pills-settings" aria-selected="false">
                            <i class="fas fa-lg fa-sliders-h"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a :class="{'active': user.user_profiles.length == 0}" class="nav-link" id="pills-profiles-tab" data-toggle="pill" href="#pills-profiles" role="tab" aria-controls="pills-profiles" aria-selected="false">
                            <i class="fas fa-lg fa-address-card"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" @click="askForLogout">
                            <i class="fas fa-lg fa-sign-out-alt"></i>
                        </a>
                        <form id="logout-form" ref="logoutForm" action="/logout" method="POST">
                            <csrf></csrf>
                        </form>
                    </li>
                </ul>
            </aside>
            <div class="col-md col-12">
                <div class="tab-content" id="pills-tabContent" :class="{'no-profiles': user.user_profiles.length == 0}">
                    <div v-if="user.user_profiles != null" class="tab-pane show" :class="{'active': user.user_profiles != null}" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <dashboard :user="user" :timeline="timeline" :mentions="mentions" :lists="lists" :loadtime="loadtime"></dashboard>
                    </div>
                    <div v-if="user.user_profiles != null" class="tab-pane" id="pills-stats" role="tabpanel" aria-labelledby="pills-stats-tab">
                        <stats :user="Object.freeze(user)"></stats>
                    </div>
                    <div class="tab-pane" id="pills-scheduled-tweets" role="tabpanel" aria-labelledby="pills-scheduled-tweets-tab">
                        <scheduled-tweets :user="user"></scheduled-tweets>
                    </div>
                    <div class="tab-pane" id="pills-settings" role="tabpanel" aria-labelledby="pills-settings-tab">
                        <settings :user="user"></settings>
                    </div>
                    <div class="tab-pane" :class="{'active': user.user_profiles.length == 0}" id="pills-profiles" role="tabpanel" aria-labelledby="pills-profiles-tab">
                        <profiles :user="user"></profiles>
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
            'timeline',
            'mentions',
            'chats',
            'lists',
            'loadtime'
        ],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },
        methods: {
            askForLogout() {
                this.$swal({
                    title: '¿Deseas cerrar sesión?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Cerrar sesión',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.$refs.logoutForm.submit();
                    }
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .app-container {
        .app-wrapper {
            height: 100%;
            min-height: 100vh;
        }
    }

    .no-profiles {
        margin: 15px;
    }

    #sidebar {
        background-color: #0f233c;

        .nav {
            flex-direction: column;

            .nav-item {
                display: flex;
                justify-content: center;
            }

            .nav-link {
                color: #64707e;
                border-radius: 0;
                transition: 75ms;
                width: 100%;
                text-align: center;
                padding: 18px 13px;

                > .fa-lg {
                    font-size: 1.5em;
                }

                &.active {
                    background-color: #21435a;
                    color: #e4e7ea;
                }

                &:hover {
                    color: #e4e7ea;
                }
            }
        }
    }

    @media (max-width: 576px) {
        .app-wrapper {
            margin-bottom: 86px!important;

            #sidebar {
                position: fixed;
                bottom: 0;
                z-index: 1;

                .nav {
                    flex-direction: row;

                    .nav-item {
                        display: flex;
                        flex: 1;
                    }
                }
            }
        }
    }
</style>
