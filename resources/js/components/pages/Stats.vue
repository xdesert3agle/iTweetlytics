<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3>Estadísticas del perfil</h3>

                <h4>Todos tus followers</h4>
                <div class="row">
                    <div class="col followers-container">
                        <ul>
                            <li v-for="(follower, i) in user.twitter_profiles[0].followers">{{ follower.twitter_user_id }}</li>
                        </ul>
                    </div>
                    <div class="col">
                        Gráfico de hace 7 días
                    </div>
                </div>

                <GChart
                    :settings="{packages: ['bar']}"
                    :data="chartData"
                    :options="chartOptions"
                    :createChart="(el, google) => new google.charts.Bar(el)"
                    @ready="onChartReady" />
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
                chartData: [
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
                        title: 'Company Performance',
                        subtitle: 'Sales, Expenses, and Profit: 2014-2017'
                    },
                    bars: 'horizontal', // Required for Material Bar Charts.
                    hAxis: { format: 'decimal' },
                    height: 400,
                    colors: ['#1b9e77', '#d95f02', '#7570b3']
                })
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
