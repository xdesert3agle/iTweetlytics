<template>
    <div class="container-fluid app-container">
        <div class="row">
            <div class="col-md-auto col-12">
                <ul class="nav flex-column nav-pills" id="pills-tab" role="tablist">
                    <li v-if="user.twitter_profiles != null" class="nav-item">
                        <a :class="{'active': user.twitter_profiles != null}" class="nav-link" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                            <i class="fas fa-lg fa-columns"></i>
                        </a>
                    </li>
                    <li v-if="user.twitter_profiles != null" class="nav-item">
                        <a class="nav-link" id="pills-stats-tab" data-toggle="pill" href="#pills-stats" role="tab" aria-controls="pills-home" aria-selected="false">
                            <i class="fas fa-lg fa-chart-line"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a :class="{'active': user.twitter_profiles.length == 0}" class="nav-link" id="pills-profiles-tab" data-toggle="pill" href="#pills-profiles" role="tab" aria-controls="pills-profiles" aria-selected="false">
                            <i class="fas fa-lg fa-address-card"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="/logout" method="POST">
                            <csrf></csrf>
                            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-md col-12">
                <div class="tab-content" id="pills-tabContent" :class="{'no-profiles': user.twitter_profiles.length == 0}">
                    <div v-if="user.twitter_profiles != null" class="tab-pane show" :class="{'active': user.twitter_profiles != null}" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <dashboard :user="user" :timeline="timeline" :mentions="mentions" :chats="chats" :lists="lists" :loadtime="loadtime"></dashboard>
                    </div>
                    <div v-if="user.twitter_profiles != null" class="tab-pane" id="pills-stats" role="tabpanel" aria-labelledby="pills-stats-tab">
                        <stats :user="Object.freeze(user)"></stats>
                    </div>
                    <div class="tab-pane" :class="{'active': user.twitter_profiles.length == 0}" id="pills-profiles" role="tabpanel" aria-labelledby="pills-profiles-tab">
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
    }
</script>

<style lang="scss" scoped>
    .app-container {
        height: 100vh;

        .row {
            height: 100%;
        }
    }

    .no-profiles {
        margin: 15px;
    }
</style>
