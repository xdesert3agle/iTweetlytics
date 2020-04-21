<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3>Estadísticas del perfil</h3>

                <div class="row">
                    <div class="col followers-container">
                        <h4>Followers</h4>
                        <div v-for="(follower, i) in user.twitter_profiles[0].followers">
                            <div class="col follower">
                                <span class="name">
                                    {{ follower.name }}
                                </span>
                                <span class="screen-name text-muted">
                                    @{{ follower.screen_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col followers-container">
                        <h4>Unfollowers recientes</h4>
                        <div v-for="(change, i) in user.twitter_profiles[0].profile_changes">
                            <div v-if="change.action == 'unfollow'" class="col follower">
                                <span class="name">
                                    {{ change.name }}
                                </span>
                                <span class="screen-name text-muted">
                                    @{{ change.screen_name }}
                                </span>
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
                    width: 600,
                    height: 400,
                    colors: ['#7570b3']
                })
            }
        },
        created() {
            this.chartData = [
                ['Fecha', 'Seguidores'],
            ];

            for (let i = 0; i < this.user.twitter_profiles[0].reports.length; i++) {
                this.chartData.push([this.parseISOString(this.user.twitter_profiles[0].reports[i].created_at), this.user.twitter_profiles[0].reports[i].profile_total_followers]);
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

    .followers-container {
        height: 800px;
        overflow: scroll;

        .follower {
            .name {
                font-weight: bold !important;
                color: $textColor;
            }

            .screen-name {
                font-weight: normal;
                color: #a7a2ce;
            }
        }
    }
</style>
