<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3>Estadísticas del perfil</h3>

                <div class="row">
                    <div class="col-auto">
                        <div class="row">
                            <div class="col">
                                <h4>Todos tus followers</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col full-column">
                                <ul>
                                    <li v-for="(follower, i) in user.current_twitter_profile[0].followers">
                                        <a :href="'https://twitter.com/' + follower.screen_name" class="profile-link">
                                            <span class="name">{{ follower.name }}</span>
                                            <span class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <h4>Nuevos followers</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col half-column">
                                        <ul>
                                            <li v-for="(follower, i) in user.current_twitter_profile[0].follows">
                                                <a :href="'https://twitter.com/' + follower.screen_name" class="profile-link">
                                                    <span class="name">{{ follower.name }}</span>
                                                    <span class="screen-name text-muted">@{{ follower.screen_name }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <h4>Unfollowers</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col half-column">
                                        <ul>
                                            <li v-for="(unfollower, i) in user.current_twitter_profile[0].unfollows">
                                                <a :href="'https://twitter.com/' + unfollower.screen_name" class="profile-link">
                                                    <span class="name">{{ unfollower.name }}</span>
                                                    <span class="screen-name text-muted">@{{ unfollower.screen_name }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <GChart
                            :settings="{packages: ['line']}"
                            :data="chartData"
                            :options="chartOptions"
                            :createChart="(el, google) => new google.charts.Line(el)"
                            @ready="onChartReady"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {GChart} from 'vue-google-charts'

    export default {
        props: [
            'user'
        ],
        components: {
            GChart
        },
        data() {
            return {
                chartsLib: null,
                // Array will be automatically processed with visualization.arrayToDataTable function
                chartData: [],
                chartData2: [
                    ['Year', 'Sales', 'Expenses', 'Profit'],
                    ['2014', 1000, 400, 200],
                    ['2015', 1170, 460, 250],
                    ['2016', 660, 1120, 300],
                    ['2017', 1030, 540, 350]
                ]
            }
        },
        computed: {
            chartOptions() {
                if (!this.chartsLib) return null;
                return this.chartsLib.charts.Line.convertOptions({
                    chart: {
                        title: 'Seguidores totales',
                        subtitle: 'Variación de los seguidores totales de los últimos 30 días'
                    },
                    colors: ['#7570b3']
                })
            }
        },
        created() {
            this.chartData = [
                ['Fecha', 'Seguidores'],
            ];

            for (let i = 0; i < this.user.current_twitter_profile[0].reports.length; i++) {
                this.chartData.push([this.parseISOString(this.user.current_twitter_profile[0].reports[i].created_at), this.user.current_twitter_profile[0].reports[i].profile_total_followers]);
            }
        },
        methods: {
            onChartReady(chart, google) {
                this.chartsLib = google
            },
            parseISOString(s) {
                let date = new Date(s);
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let dt = date.getDate();

                if (dt < 10) {
                    dt = '0' + dt;
                }

                if (month < 10) {
                    month = '0' + month;
                }

                return dt + '-' + month + '-' + year;
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    ::-webkit-scrollbar {
        width: 5px;  /* Remove scrollbar space */
        padding-left: 50px;
        background-color: lighten(black, 85%);
        //background: transparent;  /* Optional: just make scrollbar invisible */
    }

    /* Optional: show position indicator in red */
    ::-webkit-scrollbar-thumb {
        background: lighten($primaryColor, 8%);
        margin-left: 10px;
    }

    .full-column {
        height: calc(100vh - 39.82px - 15px * 2 - 4px);
        overflow: scroll;
    }

    .half-column {
        height: calc((100vh - 39.82px - 15px * 2 - 4px) / 2);
        overflow: scroll;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    .profile-link {
        &:hover {
            .name {
                text-decoration: underline;
            }
        }

        .name {
            font-weight: bold !important;
            color: $textColor;
        }

        .screen-name {
            font-weight: normal;
            color: #a7a2ce;
        }
    }
</style>
