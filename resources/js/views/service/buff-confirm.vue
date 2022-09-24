<template>
    <div class="content-wrapper page-payment">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Xác nhận thông tin {{ data.type == 'share' ? 'tăng chia sẻ bài viết Facebook' : 'tăng bình luận Facebook' }}</span></p>
                        <div class="row">
                            <div class="col-12">
                                <p class="card-description m-0">UID</p>
                                <div class="font-weight-bold  mt-1">{{ data.uid }}</div>
                            </div>
                            <div class="col-12">
                                <p class="card-description m-0">Số lượng</p>
                                <div class="font-weight-bold  mt-1">{{ data.amount | number }}</div>
                            </div>
                            <div class="col-12">
                                <p class="card-description m-0">Tổng thanh toán</p>
                                <div class="font-weight-bold  mt-1">{{ data.total_payment | numberFormat }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-description"><span class="note-title-page-payment">Nếu đã hoàn thành Buff, nhấp vào nút "Đã hoàn thành".</span></p>
                        <div class="row">
                            <div class="col d-flex">
                                <button class="btn btn-primary" v-on:click="confirmBuffComplete()" :disabled="data.status != '0' || isSending === true">Đã hoàn thành</button>
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
    let number = Intl.NumberFormat('en-IN', { maximumSignificantDigits: 3 });
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                data: {},
                isSending: false
            }
        },
        mounted() {
            this.axios
            .get('/api/facebook/buff/' + this.$route.params.id + '/confirm')
            .then(response => {
                if (response.data.result === null) (
                    this.$notify({
                        group: 'foo',
                        title: 'Không tồn tại!',
                        text,
                        duration: 10000,
                        speed: 1000
                    }),
                    this.$router.push({ name: 'home' })
                )
                this.data = response.data.result
            })
            .catch(error => {
                this.$router.push({ name: 'home' })
            });
        },
        methods: {
            confirmBuffComplete() {
                this.isSending = true
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios
                .post('/api/facebook/buff/' + this.$route.params.id + '/confirm')
                .then((response) => {
                    this.isSending = false
                    this.data.status = '1'
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
            }
        },
        filters: {
            numberFormat(value) {
                return currencyVND.format(value)
            },
            number(value) {
                return number.format(value)
            }
        }
    }
</script>