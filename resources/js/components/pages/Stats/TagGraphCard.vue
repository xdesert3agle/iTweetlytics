<template>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Tags</h4>
            <column-chart :data="graphData" width="100%" height="100%"></column-chart>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user',
            'target'
        ],
        data() {
            return {
                graphData: null,
            }
        },
        created() {
            this.fetchChartData();
        },
        methods: {
            fetchChartData() {
                axios.get('/ajax/profile/' + this.user.current_user_profile.id + '/stats/tags/' + this.target).then((response) => {
                    this.graphData = response.data;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;
    .card {
        height: 400px;

        &.small-card {
            height: 195px;
        }

        .card-body {
            display: flex;
            flex-direction: column;

            .row {
                height: 100%;
            }

            .card-title {
                font-size: 16pt;
            }
        }
    }
</style>
