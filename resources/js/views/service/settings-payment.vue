<template>
    <div class="content-wrapper service-facebook">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Cài đặt phương thức thanh toán</h4>
                        <form class="forms-sample" @submit.prevent="submitBuffShare" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="payment_type">Thanh toán</label>
                                <select class="form-control" id="payment_type" v-model="payment_type">
                                    <option value="atm">Chuyển khoản ngân hàng</option>
                                    <option value="momo">MoMo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="account_name">Tên</label>
                                <input type="text" class="form-control" placeholder="Nhập tên chủ tài khoản" id="account_name" v-model="account_name">
                            </div>
                            <div class="form-group" v-if="payment_type === 'atm'">
                                <label for="bank_code">Tài khoản ngân hàng/Số thẻ</label>
                                <input type="text" class="form-control" placeholder="Nhập tài khoản ngân hàng/số thẻ" id="bank_code" v-model="bank_code">
                            </div>
                            <div class="form-group" v-if="payment_type === 'atm'">
                                <label for="bank_name">Tên ngân hàng</label>
                                <input type="text" class="form-control" placeholder="Nhập tên ngân hàng" id="bank_name" v-model="bank_name">
                            </div>
                            <div class="form-group" v-if="payment_type === 'atm'">
                                <label for="bank_branch">Chi nhánh</label>
                                <input type="text" class="form-control" placeholder="Nhập chi nhánh" id="bank_branch" v-model="bank_branch">
                            </div>
                            <div class="form-group" v-if="payment_type === 'momo'">
                                <label for="bank_code">Số điện thoại</label>
                                <input type="text" class="form-control" placeholder="Nhập số điện thoại" id="bank_code" v-model="bank_code">
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="(payment_type === 'atm' && (account_name === '' || bank_code === '' || bank_name === '' || bank_branch === '')) || (payment_type === 'momo' && bank_code === '') || isSending === true">Lưu</button>
                        </form>
                    </div>

                    <div class="card-body">
                        <p class="card-description">Phương thức thanh toán</p>
                        <div class="table-borderless table-hover">
                            <table class="table">
                                <tbody v-for="(payment, index) in paymentList" :key="index">
                                    <tr v-if="payment.payment_type !== null">
                                        <td>
                                            Thanh toán
                                            <div class="font-weight-bold mt-1" v-bind:style="{color: '#F7A76C'}">{{ payment.payment_type === 'atm' ? 'Chuyển khoản ngân hàng' : 'MoMo'}}</div>
                                        </td>
                                        <td>
                                            Tên
                                            <div class="font-weight-bold mt-1">{{ payment.account_name }}</div>
                                        </td>
                                        <td v-if="payment.payment_type === 'atm'">
                                            Tài khoản ngân hàng/Số thẻ
                                            <div class="font-weight-bold mt-1">{{ payment.bank_code }}</div>
                                        </td>
                                        <td v-if="payment.payment_type === 'atm'">
                                            Tên ngân hàng
                                            <div class="font-weight-bold mt-1">{{ payment.bank_name }}</div>
                                        </td>
                                        <td v-if="payment.payment_type === 'atm'">
                                            Chi nhánh
                                            <div class="font-weight-bold mt-1">{{ payment.bank_branch }}</div>
                                        </td>
                                        <td v-if="payment.payment_type === 'momo'">
                                            Số điện thoại
                                            <div class="font-weight-bold mt-1">{{ payment.bank_code }}</div>
                                        </td>
                                        <td v-if="payment.payment_type === 'momo'"></td>
                                        <td v-if="payment.payment_type === 'momo'"></td>
                                        <td>
                                            <div class="font-weight-bold mt-1" v-bind:style="{color: '#FF1E00', cursor: 'pointer'}" v-on:click="paymentDeleteMethodSetting(index)"><i class="fa-solid fa-trash-can"></i></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    let dollarUS = Intl.NumberFormat("en-US");
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                payment_type: 'atm',
                account_name: '',
                bank_code: '',
                bank_name: '',
                bank_branch: '',
                paymentList: [],
                isSending: false,
            }
        },
        mounted() {
            this.axios
            .get('/api/system-settings/index')
            .then(response => {
                this.paymentList = response.data.result
            })
            .catch(error => {
                console.log(error)
                return
            });
        },
        methods: {
            submitBuffShare(e) {
                this.isSending = true
                axios.post('/api/system-settings/payment-settings', {
                    payment_type: this.payment_type,
                    account_name: this.account_name,
                    bank_code: this.bank_code,
                    bank_name: this.bank_name,
                    bank_branch: this.bank_branch
                })
                .then((response) => {
                    this.paymentList = response.data.result

                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    });
                    this.isSending = false
                })
                .catch((error) => {
                    return
                });
            },
            paymentDeleteMethodSetting(index) {
                axios
                .post('/api/system-settings/payment-settings/' + this.paymentList[index].id + '/delete')
                .then((response) => {
                    this.paymentList.splice(index, 1)
                    this.$notify({
                        group: 'foo',
                        title: response.data.message,
                        text,
                        duration: 10000,
                        speed: 1000
                    });
                })
                .catch((error) => {
                    return
                });
            },
        }
    }
</script>