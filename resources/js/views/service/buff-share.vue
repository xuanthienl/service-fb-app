<template>
    <div class="content-wrapper service-facebook">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">TĂNG CHIA SẺ BÀI VIẾT FACEBOOK</h4>
                        <form class="forms-sample" @submit.prevent="submitBuffShare" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="uid">Link/uid bài viết</label>
                                <input type="text" class="form-control" placeholder="Nhập link/uid bài viết" id="uid" v-model="uid">
                            </div>
                            <div class="form-group">
                                <label for="channel">Kênh</label>
                                <select class="form-control" id="channel" v-model="channel">
                                    <option v-for="(channel, index) in channelsList" :value="index" :key="index">Kênh {{channel.channel_name}} giá {{channel.channel_price}} Xu</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Số lượng</label>
                                <input type="number" class="form-control" placeholder="Nhập số lượt Share" id="amount" :min="amount_min" :max="amount_max" v-model="amount">
                            </div>
                            <div class="form-group d-flex flex-row-reverse">
                                <p class="card-description">Xu</p>
                                <p class="card-description mx-1" style="color:red; font-weight: bold;">{{ total_payment }}</p>
                                <p class="card-description">Tổng thanh toán:</p>
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="uid === '' || amount === '' || channel === '' || isSending === true">Mua ngay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between">
                            <h4 class="card-title mb-3">ĐƠN MUA</h4>
                        </div>
                        <p class="card-description">
                            Lịch sử sử dụng dịch vụ của <span class="note-color">{{user.name != '' ? user.name : user.username}}</span> trong 10 lần gần nhất.
                        </p>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody v-for="(order, index) in orderList" :key="index">
                                    <tr>
                                        <td>
                                            Thời gian
                                            <div class="font-weight-bold  mt-1">{{ order.created_at | dateFormat }}</div>
                                        </td>
                                        <td>
                                            Thanh toán
                                            <div class="font-weight-bold text-success  mt-1">{{ order.total_payment | currencyFormat }}</div>
                                        </td>
                                        <td>
                                            Trạng thái
                                            <div class="font-weight-bold  mt-1" v-bind:style="{color: order.status == 0 ? '#FFCB42' : '#5BB318'}">{{ order.status == 0 ? 'Chờ xử lý' : 'Hoàn thành' }}</div>
                                        </td>
                                        <td>
                                            UID
                                            <div class="font-weight-bold  mt-1"><a :href="order.uid" target="_blank">{{ order.uid }}</a></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fa-solid fa-book"></i>THÔNG TIN CHI TIẾT</h4>
                    <div class="forms-sample" v-for="(channel, index) in channelsList" :key="index">
                        <div class="form-group">
                            <label>Kênh {{ channel.channel_name }}:</label>
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
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                uid: '',
                amount: '',
                channel: '',
                total_payment: 0,

                channelsList: [],
                amount_min: '',
                amount_max: '',
                orderList: [],
                
                isSending: false
            }
        },
        mounted() {
            this.axios
            .get('/api/facebook/settings')
            .then(response => {
                for (let data of response.data.result) {
                    if (data['type'] === 'channel' && data['channel_type'] === 'share') {
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
                }
            })
            .catch(error => {
                console.log(error)
                return
            });

            // đơn mua
            this.axios
            .get('/api/facebook/order-share/' + this.user.id)
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
                this.total_payment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price)
            },
            amount() {
                if (this.uid !== '') {
                this.total_payment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price)
                }
            },
            channel() {
                if (this.uid !== '') {
                this.total_payment = NumberFormatEN.format(this.amount * this.channelsList[this.channel].channel_price)
                }
            }
        },
        methods: {
            submitBuffShare(e) {
                this.isSending = true
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/facebook/buff-share', {
                    user_id: this.user.id,
                    uid: this.uid,
                    amount: this.amount,
                    channel: this.channelsList[this.channel].id,
                })
                .then((response) => {
                    console.log(response)
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
            }
        }
    }
</script>