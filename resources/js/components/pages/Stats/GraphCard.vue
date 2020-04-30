<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ card_title }}{{stat.is_accumulated ? ' en ' + timeIntervalString : ""}}</h4>
                        </div>
                        <div class="col-auto text-right">
                            <select class="form-control" @input="timeIntervalChanged">
                                <option value="weekly" selected>7 días</option>
                                <option value="biweekly">14 días</option>
                                <option value="monthly">30 días</option>
                                <option value="yearly">1 año</option>
                            </select>
                        </div>
                    </div>
                    <div class="stat-container">
                        <span class="stat-amount">{{ stat.value }}</span>
                        <span v-if="!stat.is_accumulated" class="stat-variation" :class="{'increase': stat.variation > 0, 'decrease': stat.variation < 0}">
                            <i v-if="stat.variation > 0" class="fa fa-lg fa-caret-up"></i>
                            <i v-else-if="stat.variation < 0" class="fa fa-lg fa-caret-down"></i>
                            {{ stat.variation }}
                        </span>
                    </div>
                    <div class="row no-gutters modal-trigger-row">
                        <div class="col">
                            <button-modal :id="id" :title="modal_title" :button="false">
                                <template slot="button">
                                    <slot name="modal-trigger"></slot>
                                </template>
                                <template slot="modal-body">
                                    <slot name="modal"></slot>
                                </template>
                            </button-modal>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <line-chart :data="graphData" width="100%"></line-chart>
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
            'id',
            'card_title',
            'stat_endpoint',
            'modal_title'
        ],
        data() {
            return {
                graphData: null,
                stat: {
                    value: null,
                    variation: null,
                    is_accumulated: null
                },
                timeInterval: 'weekly'
            }
        },
        computed: {
            timeIntervalString() {
                switch (this.timeInterval) {
                    case 'weekly':
                        return "los últimos 7 días";

                    case 'biweekly':
                        return "los últimos 14 días";

                    case 'monthly':
                        return "los últimos 30 días";

                    case 'yearly':
                        return "el último año";
                }
            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                axios.get(this.stat_endpoint + "/" + this.timeInterval + "/")
                    .then((response) => {
                        this.stat = response.data.stat;
                        this.graphData = response.data.graph;
                    });
            },
            timeIntervalChanged($event) {
                this.timeInterval = $event.target.value;

                this.fetchData();
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .card {
        //height: 100%;

        .card-body {
            display: flex;
            flex-direction: column;
            max-height: 100vh;
            overflow: hidden;

            .card-title {
                font-size: 16pt;
            }

            .card-content {
                align-items: center;
            }

            .followers-list-container, .unfollowers-list-container {
                margin-bottom: 2em;

                a {
                    color: inherit;

                    &:hover {
                        text-decoration: none;
                    }
                }
            }

            .recent-unfollowers-container {
                max-height: 300px;
                overflow-y: scroll;

                ::-webkit-scrollbar {
                    width: 10px !important;
                }
            }

            .stat-amount {
                color: $primaryColor;
                line-height: initial;

                font-size: 32pt;
                font-weight: bold;
            }

            .stat-variation {
                color: $primaryColor;
                line-height: initial;

                font-size: 20pt;
                font-weight: 500;

                &.increase {
                    color: green;
                }

                &.decrease {
                    color: red;
                }
            }

            .modal-trigger-row {
                margin: 10px 0 15px 0;
            }
        }
    }
</style>
