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
                                <p class="card-description m-0">Mã lệnh</p>
                                <div class="font-weight-bold  mt-1">{{ code }}</div>
                            </td>
                            <td class="col">
                                <p class="card-description m-0">Chuyển khoản</p>
                                <div class="font-weight-bold  mt-1">{{ payment_type === 'atm' ? 'Chuyển khoản ngân hàng' : 'Momo' }}</div>
                            </td>
                        </tr>
                    </div>

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Nếu đã nhận tiền, nhấp vào nút "Đã nhận tiền".</span></p>
                        <div class="row">
                            <div class="col d-flex">
                                <button class="btn btn-primary" v-on:click="confirmSellPayment()" :disabled="status != '1' || isSending === true">Đã nhận tiền</button>
                                <button class="btn-cancel pl-4" v-on:click="confirmSellPaymentFail()" :disabled="status != '1' || isSending === true">Chưa nhận được tiền</button>
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
                price: '',
                payment_type: '',
                status: '',
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
                if (this.user.roles != '1') {
                    this.$notify({
                        group: 'foo',
                        title: 'Bạn không được phép truy cập vào đường dẫn này.',
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'payment' })
                }
                this.id = response.data.result.id
                this.code = response.data.result.code
                this.price = response.data.result.price
                this.payment_type = response.data.result.payment_type
                this.status = response.data.result.status
            })
            .catch(error => {
                this.$router.push({ name: 'payment' })
            });
        },
        methods: {
            confirmSellPayment() {
                this.isSending = true
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/sell-currency/' + this.id + '/confirm')
                .then((response) => {
                    this.isSending = false
                    this.status = '2'
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    })
                })
                .catch((error) => {
                    this.isSending = false
                    return
                });
            },
            confirmSellPaymentFail() {
                this.isSending = true
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/sell-currency/' + this.id + '/confirm-fail')
                .then((response) => {
                    this.isSending = false
                    this.status = '3'
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    })
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