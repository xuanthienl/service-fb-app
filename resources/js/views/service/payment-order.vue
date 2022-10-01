<template>
    <div class="content-wrapper page-payment">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Xác nhận thông tin lệnh</span></p>
                        <tr class="row">
                            <td class="col">
                                <p class="card-description m-0">Số lượng</p>
                                <div class="font-weight-bold  mt-1">{{ price | numberFormat }}</div>
                            </td>
                            <td class="col">
                                <p class="card-description m-0">Giá</p>
                                <div class="font-weight-bold  mt-1">{{ 1 | numberFormat }}</div>
                            </td>
                            <td class="col">
                                <p class="card-description m-0">Số lượng</p>
                                <div class="font-weight-bold  mt-1">{{ amount_coin }} Xu</div>
                            </td>
                        </tr>
                    </div>

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Chuyển tiền vào tài khoản được cung cấp bên dưới.</span></p>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="card-description m-0">{{ payment_type | checkPaymentType }}</p>
                            </div>

                            <div class="col-md-4">
                                <form class="forms-sample">
                                    <div class="form-group">
                                        <p class="card-description m-0">Nội dung chuyển khoản</p>
                                        <div class="font-weight-bold  mt-1">{{ code }}</div>
                                    </div>
                                    <div class="form-group">
                                        <p class="card-description m-0">Tên</p>
                                        <div class="font-weight-bold  mt-1">{{ account_name }}</div>
                                    </div>
                                    <div class="form-group" v-if="payment_type === 'atm'">
                                        <p class="card-description m-0">Tài khoản ngân hàng/Số thẻ</p>
                                        <div class="font-weight-bold  mt-1">{{ bank_code }}</div>
                                    </div>
                                    <div class="form-group">
                                        <p class="card-description m-0" v-if="payment_type === 'atm'">Tên ngân hàng</p>
                                        <div class="font-weight-bold  mt-1">{{ bank_name }}</div>
                                    </div>
                                    <div class="form-group" v-if="payment_type === 'momo'">
                                        <p class="card-description m-0">Số điện thoại</p>
                                        <div class="font-weight-bold  mt-1">{{ bank_code }}</div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-4">
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Sau khi chuyển tiền, nhấp vào nút "Đã chuyển tiền".</span></p>
                        <div class="row">
                            <div class="col d-flex">
                                <button class="btn btn-primary" v-on:click="confirmBuyPayment()" :disabled="status != '0' || isSending === true">Đã chuyển tiền</button>
                                <button class="btn-cancel pl-4" v-on:click="cancelBuyPayment()" :disabled="status != '0' || isSending === true">Huỷ lệnh</button>
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
    let currencyVND = Intl.NumberFormat("vi-VN", { style: 'currency', currency: 'VND' });
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                code: '',
                status: '',
                price: '',
                amount_coin: '',
                payment_type: '',

                account_name: '',
                bank_branch: '',
                bank_code: '',
                bank_name: '',

                isSending: false
            }
        },
        mounted() {
            this.axios
            .get('/api/payment/buy-currency/' + this.$route.params.order_code)
            .then(response => {
                if (response.data.result === null) (
                    this.$notify({
                        group: 'foo',
                        title: 'Lệnh mua không tồn tại.',
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                )
                if (response.data.result.user_id != this.user.id && this.user.roles != '1') {
                    this.$notify({
                        group: 'foo',
                        title: 'Lệnh mua này không phải của bạn.',
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                }
                if (response.data.settings === null) (
                    this.$notify({
                        group: 'foo',
                        title: 'Đang có lỗi xảy ra. Vui lòng thử lại sau.',
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                )
                this.id = response.data.result.id
                this.code = response.data.result.code
                this.price = response.data.result.price
                this.status = response.data.result.status
                this.amount_coin = response.data.result.amount_coin
                this.payment_type = response.data.result.payment_type
                this.account_name = response.data.settings.account_name
                this.bank_branch = response.data.settings.bank_branch
                this.bank_code = response.data.settings.bank_code
                this.bank_name = response.data.settings.bank_name
            })
            .catch(error => {
                this.$router.push({ name: 'payment' })
            });
        },
        methods: {
            cancelBuyPayment() {
                this.isSending = true
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/buy-currency/' + this.id + '/delete')
                .then((response) => {
                    this.isSending = false
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                })
                .catch((error) => {
                    this.isSending = false
                    return
                });
            },
            confirmBuyPayment() {
                this.isSending = true
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/buy-currency/' + this.id + '/confirm')
                .then((response) => {
                    this.isSending = false
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                })
                .catch((error) => {
                    this.isSending = false
                    return
                });
            },
        },
        filters: {
            numberFormat(value) {
                return currencyVND.format(value)
            },
            checkPaymentType(value) {
                if (value == 'atm') {
                    return 'Chuyển khoản ngân hàng'
                }
                return 'Chuyển khoản Momo'
            }
        }
    }
</script>