<template>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Đơn hàng của bạn</div>
                        <div class="card-stats-items">
                        <div class="card-stats-item">
                            <div class="card-stats-item-count">{{orderPending.length}}</div>
                            <div class="card-stats-item-label">Chờ xử lý</div>
                        </div>
                        <div class="card-stats-item">
                            <div class="card-stats-item-count">{{orderCompleted.length}}</div>
                            <div class="card-stats-item-label">Hoàn thành</div>
                        </div>
                        <div class="card-stats-item">
                            <div class="card-stats-item-count">{{orderFail.length}}</div>
                            <div class="card-stats-item-label">Thất bại</div>
                        </div>
                        </div>
                    </div>

                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Tổng số đơn hàng</h4>
                        </div>
                        <div class="card-body">
                            {{ orderList.length }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card card-statistic-2">
                <div class="card-stats">
                    <div class="card-stats-title">Ví của bạn</div>
                    <div class="card-stats-items">
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{paymentPending.length}}</div>
                        <div class="card-stats-item-label">Chờ xử lý</div>
                    </div>
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{paymentCompleted.length}}</div>
                        <div class="card-stats-item-label">Hoàn thành</div>
                    </div>
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{paymentFail.length}}</div>
                        <div class="card-stats-item-label">Thất bại</div>
                    </div>
                    </div>
                </div>

                <div class="card-icon shadow-primary bg-primary">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                    <h4>Tổng số dư DONATE</h4>
                    </div>
                    <div class="card-body">
                    {{ loggedIn === true ? user.coin : 0 | currencyFormat }}
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card card-statistic-2">
                <div class="card-stats">
                    <div class="card-stats-title">SỬ DỤNG DỊCH VỤ TRONG HÔM NAY</div>
                    <div class="card-stats-items">
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{orderTodayPending.length}}</div>
                        <div class="card-stats-item-label">Chờ xử lý</div>
                    </div>
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{orderTodayCompleted.length}}</div>
                        <div class="card-stats-item-label">Hoàn thành</div>
                    </div>
                    <div class="card-stats-item">
                        <div class="card-stats-item-count">{{orderTodayFail.length}}</div>
                        <div class="card-stats-item-label">Thất bại</div>
                    </div>
                    </div>
                </div>

                <div class="card-icon shadow-primary bg-primary">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                    <h4>Tổng số đơn dịch vụ</h4>
                    </div>
                    <div class="card-body">
                    {{ orderTodayList.length }}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                orderList: [],
                orderPending: [],
                orderCompleted: [],
                orderFail: [],
                paymentList: [],
                paymentPending: [],
                paymentCompleted: [],
                paymentFail: [],
                orderTodayList: [],
                orderTodayPending: [],
                orderTodayCompleted: [],
                orderTodayFail: [],
            }
        },
        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn
            },
            getUser() {
                return this.$store.getters.getUser
            }
        },
        mounted() {
            this.axios
            .get('/api/facebook/order/total')
            .then(response => {
                this.orderTodayList = response.data.result
                this.orderTodayPending = this.orderTodayList.filter(item => item.status == 0)
                this.orderTodayCompleted = this.orderTodayList.filter(item => item.status == 1)
                this.orderTodayFail = this.orderTodayList.filter(item => item.status != 0 && item.status != 1)
            })
            .catch(error => {
                return
            });

            if(!this.$store.getters.loggedIn) {
                return
            }

            this.axios
            .get('/api/facebook/order/' + this.user.id + '/total')
            .then(response => {
                this.orderList = response.data.result
                this.orderPending = this.orderList.filter(item => item.status == 0)
                this.orderCompleted = this.orderList.filter(item => item.status == 1)
                this.orderFail = this.orderList.filter(item => item.status != 0 && item.status != 1)
            })
            .catch(error => {
                return
            });

            this.axios
            .get('/api/payment/' + this.user.id)
            .then(response => {
                this.paymentList = response.data.result
                this.paymentPending = this.paymentList.filter(item => item.status == 1)
                this.paymentCompleted = this.paymentList.filter(item => item.status == 2)
                this.paymentFail = this.paymentList.filter(item => item.status == 3)
            })
            .catch(error => {
                return
            });
        },
        filters: {
            currencyFormat(value) {
                return Intl.NumberFormat('en-US').format(value)
            }
        }
    }
</script>