<template>
    <div class="content-wrapper page-payment">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Nạp xu</h4>
                        <form class="form-sample">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-description">Giá <span class="note-page-payment">1 Xu = 1 VND</span></p>
                                    <p class="card-description">Giới hạn thời gian thanh toán <span class="note-page-payment">15 phút</span></p>
                                    <p class="card-description">Giới hạn số lượng thanh toán<span class="note-page-payment">10,000 VND trở lên</span></p>
                                    <p class="card-description">Phương thức thanh toán <span class="note-page-payment">Chuyển khoản ngân hàng / Momo</span></p>
                                    <p class="card-description mt-5"><span class="note-title-page-payment">Điều khoản và điều kiện</span></p>
                                    <p class="card-description">- Chuyển khoản 24/7</p>
                                    <p class="card-description">- Ghi đúng nội dung chuyển khoản</p>
                                    <p class="card-description">- Khi thanh toán hoàn thành sẽ nhận xu sau 1 đến 30 phút</p>
                                    <p class="card-description">- Nếu thao tác sai hoặc không nhận được xu vui lòng liên hệ <router-link :to="{name: 'contact'}" class="note-page-payment p-0">Trợ giúp & hỗ trợ</router-link></p>
                                    <p class="card-description">- Nhấn F5 nếu sau 30 phút chưa nhận được xu</p>
                                </div>

                                <div class="col-md-6">
                                    <form class="forms-sample" @submit.prevent="submitBuyCurrency" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="payment_type">Thanh toán</label>
                                            <select class="form-control" id="payment_type" v-model="payment_type">
                                                <option value="atm">Chuyển khoản ngân hàng</option>
                                                <option value="momo">Momo</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="currency_vnd">Tôi muốn trả</label>
                                            <input type="number" class="form-control" placeholder="10,000 VND" id="currency_vnd" :min="10000" v-model="currency_vnd">
                                        </div>
                                        <div class="form-group">
                                            <label for="currency_xu">Tôi sẽ nhận được</label>
                                            <input type="text" class="form-control" placeholder="0.00 Xu" id="currency_xu" v-model="currency_xu" :disabled="true">
                                        </div>
                                        <button type="submit" class="btn btn-primary" :disabled="currency_vnd === '' || isSending === true">Mua</button>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <h4>Lịch sử nạp xu</h4>
                    <p>Lịch sử nạp xu của <span class="note-color">{{user.name != null ? user.name : user.username}}</span> trong 10 lần gần nhất.</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th>Thời gian</th>
                                <th>Mã lệnh</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                            </tr>
                            <tr v-for="(payment, index) in paymentList" :key="index">
                                <td>{{ payment.created_at | dateFormat }}</td>
                                <td><router-link :to="{name: 'payment-order', params: { order_code: payment.code } }" class="note-page-payment text-success font-weight-bold p-0">{{ payment.code }}</router-link></td>
                                <td>{{ payment.amount_coin | numberFormat }}</td>
                                <td><div class="badge" v-bind:class="[payment.status == 2 ? 'badge-primary' : 'badge-warning']">{{ payment.status | checkStatus }}</div></td>
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
    </div>
</template>

<script>
    import moment from 'moment';
    moment.locale('vi');
    const text = `${moment().calendar()}`
    let currencyVND = Intl.NumberFormat("vi-VN", { style: 'currency', currency: 'VND' });
    let inputCurrencyVND = Intl.NumberFormat("vi-VN");
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                payment_type: 'atm',
                currency_vnd: '',
                currency_xu: '',
                paymentList: [],
                isSending: false
            }
        },
        mounted() {
            this.$store.dispatch('loadCurrency')
            this.axios
            .get('/api/payment/' + this.user.id)
            .then(response => {
                this.paymentList = response.data.result
            })
            .catch(error => {
                this.$router.push({ name: 'home' })
            });
        },
        watch: {
            currency_vnd() {
                this.currency_xu = inputCurrencyVND.format(this.currency_vnd)
                if (this.currency_xu == 0) {
                    this.currency_xu = ''
                }
            }
        },
        methods: {
            submitBuyCurrency(e) {
                this.isSending = true
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/buy-currency', {
                    user_id: this.user.id,
                    payment_type: this.payment_type,
                    currency: this.currency_vnd
                })
                .then((response) => {
                    this.isSending = false
                    this.$router.push({ name: 'payment-order', params: { order_code: response.data.result } })
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
            numberFormat(value) {
                return currencyVND.format(value)
            },
            checkStatus(value) {
                if (value == '0') {
                    return 'Chưa hoàn thành'
                } else if (value == '1') {
                    return 'Chờ xử lý'
                } else if (value == '2') {
                    return 'Hoàn thành'
                } else if (value == '3') {
                    return 'Thất bại'
                }
            }
        }
    }
</script>