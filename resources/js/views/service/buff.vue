<template>
    <div class="content-wrapper service-facebook">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">TĂNG {{type}} FACEBOOK</h4>
                        <form class="forms-sample" @submit.prevent="submitBuff" enctype="multipart/form-data">

                            <div class="form-group" :hidden="hiddenUid">
                                <label for="uid">Link/uid bài viết</label>
                                <input type="text" class="form-control" placeholder="Nhập link/uid bài viết" id="uid" v-model="uid">
                            </div>

                            <div class="form-group" :hidden="hiddenChannel">
                                <label for="channel">Kênh</label>
                                <select class="form-control" id="channel" v-model="channel">
                                    <option v-for="(channel, index) in channelsList" :value="index" :key="index">Kênh {{channel.channel_name}} giá {{channel.channel_price}} Xu</option>
                                </select>
                            </div>

                            <div class="form-group" :hidden="hiddenAmount">
                                <label for="amount">Số lượng</label>
                                <input type="number" class="form-control" placeholder="Nhập số lượng" id="amount" :min="amount_min" :max="amount_max" v-model="amount">
                            </div>

                            <div class="form-group" :hidden="hiddenMinutes">
                                <label for="amount">Số phút</label>
                                <input type="number" class="form-control" placeholder="Nhập số phút duy trì" id="minutes" :min="minutes_min" :max="minutes_max" v-model="minutes">
                            </div>

                            <div class="form-group d-flex flex-row-reverse">
                                <p class="card-description">Xu</p>
                                <p class="card-description mx-1" style="color:red; font-weight: bold;">{{ totalPayment }}</p>
                                <p class="card-description">Tổng thanh toán:</p>
                            </div>

                            <button type="submit" class="btn btn-primary" v-bind:class="[isSending === true ? 'btn-progress' : '']" :disabled="uid === '' || amount === '' || channel === '' || isSending === true">Mua ngay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <h4>Đơn mua</h4>
                    <p>Lịch sử sử dụng dịch vụ của <span class="note-color">{{user.name != null ? user.name : user.username}}</span> trong 10 lần gần nhất.</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Thời gian</th>
                                <th>Thanh Toán</th>
                                <th>UID</th>
                                <th>Trạng thái</th>
                            </tr>
                            <tr v-for="(order, index) in orderList" :key="index">
                                <td>{{ order.created_at | dateFormat }}</td>
                                <td>{{ order.total_payment | currencyFormatVND }}</td>
                                <td><a :href="order.uid" target="_blank">{{ order.uid }}</a></td>
                                <td><div class="badge" v-bind:class="[order.status == 0 ? 'badge-warning' : 'badge-primary']">{{ order.status | checkStatus }}</div></td>
                            </tr>
                        </table>
                    </div>
                    <div class="float-right">
                        <nav>
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">THÔNG TIN CHI TIẾT</h4>
                    <div class="forms-sample" v-for="(channel, index) in channelsList" :key="index">
                        <div class="form-group">
                            <label :hidden="hiddenChannel">Kênh {{ channel.channel_name }}:</label>
                            <p class="card-description" style="white-space: pre-wrap;">{{ channel.channel_description }}</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    moment.locale('vi');
    const text = `${moment().calendar()}`
    let NumberFormatEN = Intl.NumberFormat('en-US');
    let NumberFormatVND = Intl.NumberFormat("vi-VN", { style: 'currency', currency: 'VND' });
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                type: this.$route.query.type,
                uid: '',
                amount: '',
                channel: '',
                minutes: '',
                amount_min: '',
                amount_max: '',
                minutes_min: '',
                minutes_max: '',

                orderList: [],
                channelsList: [],
                totalPayment: 0,
                isSending: false,

                hiddenUid: false,
                hiddenChannel: false,
                hiddenAmount: false,
                hiddenMinutes: true
            }
        },
        mounted() {
            // Set Input Form
            if (this.type == 'like' || this.type == 'view' || this.type == 'live') {
                this.hiddenChannel = true
            }
            if (this.type == 'live') {
                this.hiddenMinutes = false
            }

            // Get Data Setting Facebook
            this.axios
            .get('/api/facebook/settings')
            .then(response => {
                for (let data of response.data.result) {
                    if (data['type'] === 'channel' && data['channel_type'] === this.type) {
                        if (this.channel === '') {
                        this.channel = 0
                        }
                        this.channelsList.push(data)
                    }

                    if (data['type'] === 'amount') {
                        this.amount_min = data['amount_min']
                        this.amount_max = data['amount_max']
                        this.amount = this.amount_min
                    }

                    if (data['type'] === 'minutes' && this.hiddenMinutes === false) {
                        this.minutes_min = data['minutes_min']
                        this.minutes_max = data['minutes_max']
                        this.minutes = this.minutes_min
                    } else {
                        this.minutes = 1
                    }
                }
            })
            .catch(error => {
                console.log(error)
                return
            });

            // Get Order List 10 Record
            this.axios
            .get('/api/facebook/order/' + this.user.id + '/' + this.type)
            .then(response => {
                this.orderList = response.data.result
            })
            .catch(error => {
                console.log(error)
                return
            });
        },
        watch: {
            uid() {
                this.totalPayment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price * this.minutes)
            },
            amount() {
                if (this.uid !== '') {
                this.totalPayment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price * this.minutes)
                }
            },
            channel() {
                if (this.uid !== '') {
                this.totalPayment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price * this.minutes)
                }
            },
            minutes() {
                if (this.uid !== '') {
                this.totalPayment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price * this.minutes)
                }
            }
        },
        methods: {
            submitBuff(e) {
                this.isSending = true
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/facebook/buff', {
                    user_id: this.user.id,
                    type: this.type,
                    uid: this.uid,
                    amount: this.amount,
                    channel: this.channelsList[this.channel].id,
                    minutes: this.minutes,
                })
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    });
                    this.isSending = false

                    if (response.data.result === false) {
						return
					}
                    var totalPayment = this.amount * this.channelsList[this.channel].channel_price
                    this.orderList.unshift({
                        'created_at': moment().format(),
                        'total_payment': totalPayment,
                        'status': 0,
                        'uid': this.uid
                    })
                    if (this.orderList.length > 10) {
                    	this.orderList.pop();
					}
                    this.$store.dispatch('servicePayment', totalPayment)
                })
                .catch((error) => {
                    this.isSending = false
                    return
                });
            },
        },
        filters: {
            dateFormat(value) {
                return moment(value).format("ddd, HH:mm")
            },
            currencyFormat(value) {
                return NumberFormatEN.format(value)
            },
            currencyFormatVND(value) {
                return NumberFormatVND.format(value)
            },
            checkStatus(value) {
                if (value == '0') {
                    return 'Chờ xử lý'
                } else if (value == '1') {
                    return 'Hoàn thành'
                } else if (value == '2') {
                    return 'Thất bại'
                }
            }
        }
    }
</script>