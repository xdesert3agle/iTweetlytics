<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3>Estadísticas del perfil</h3>

                <h4>Followers</h4>
                <div class="row">
                    <div class="col followers-container">
                        <ul>
                            <li v-for="(follower, i) in user.twitter_profiles[0].reports.profile_total_followers">{{ follower.twitter_user_id }}</li>
                        </ul>
                    </div>
                    <div class="col">
                        <GChart
                            :settings="{packages: ['bar']}"
                            :data="chartData"
                            :options="chartOptions"
                            :createChart="(el, google) => new google.charts.Bar(el)"
                            @ready="onChartReady" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { GChart } from 'vue-google-charts'

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
            chartOptions () {
                if (!this.chartsLib) return null;
                return this.chartsLib.charts.Bar.convertOptions({
                    chart: {
                        title: 'Seguidores totales',
                        subtitle: 'Variación de los seguidores totales de los últimos 30 días'
                    },
                    bars: 'vertical', // Required for Material Bar Charts.
                    hAxis: { format: 'decimal' },
                    height: 400,
                    colors: ['#1b9e77', '#d95f02', '#7570b3']
                })
            }
        },
        created() {
            this.chartData = [
                ['Year', 'Seguidores'],
            ];

            for (let i = 0; i < this.user.twitter_profiles[0].reports.length; i++) {
                this.chartData.push([this.user.twitter_profiles[0].reports[i].created_at, this.user.twitter_profiles[0].reports[i].profile_total_followers]);
            }
        },
        methods: {
            onChartReady (chart, google) {
                this.chartsLib = google
            }
        }
    }
</script>

<style lang="scss" scoped>
    .followers-container {
        height: 80%;
        overflow: scroll;
    }
</style>
