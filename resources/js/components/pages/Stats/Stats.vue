<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Estadísticas del perfil</h3>
                    </div>
                </div>

                <div class="row stat-list-container">
                    <div class="col">
                        <ul class="nav nav-tabs stat-list" id="myTab" role="tablist">
                            <li class="nav-item">
                                <button class="btn active" id="unfollower-stats-tab" data-toggle="tab" href="#unfollower-stats" role="tab" aria-controls="unfollower-stats" aria-selected="true">
                                    Seguidores
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="btn" id="friends-stats-tab" data-toggle="tab" href="#friends-stats" role="tab" aria-controls="friends-stats" aria-selected="false">
                                    Seguidos
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row no-gutters">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="row no-gutters profile-stats-container tab-pane fade show active" id="unfollower-stats" role="tabpanel" aria-labelledby="unfollower-stats-tab">
                                <div class="col-12">
                                    <followers-stats :user="user"></followers-stats>
                                </div>
                            </div>
                            <div class="row no-gutters profile-stats-container tab-pane fade" id="friends-stats" role="tabpanel" aria-labelledby="friends-stats-tab">
                                <div class="col-12">
                                    <friends-stats :user="user"></friends-stats>
                                </div>
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
        props: [
            'user'
        ],
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .tab-content {
        .active {
            display: flex;
        }
    }

    .stat-list-container {
        padding-bottom: 1em;

        .stat-list {
            border: 0;

            li {
                &:not(:first-child) {
                    margin-left: 10px !important;
                }

                button {
                    padding: 6px 20px !important;
                    background-color: #e6e6e6 !important;
                    border: 1px solid #d7d7d7 !important;
                    color: #444 !important;
                    transition: 200ms !important;

                    &:hover {
                        background-color: #d7d7d7 !important;
                    }
                }
            }
        }
    }

    ::-webkit-scrollbar {
        width: 10px; /* Remove scrollbar space */
        padding-left: 50px;
        background-color: lighten(black, 85%);
        //background: transparent;  /* Optional: just make scrollbar invisible */
    }

    /* Optional: show position indicator in red */
    ::-webkit-scrollbar-thumb {
        background: lighten($primaryColor, 8%);
        margin-left: 10px;
    }
</style>
