<template>
    <div class="content-wrapper page-payment">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample">
                            <div class="row">
                                <div class="col-12">
                                    <form class="forms-sample" @submit.prevent="submitSearch">
                                        <div class="form-group">
                                            <label for="key">Tìm kiếm</label>
                                            <input type="text" class="form-control" placeholder="Nhập Username cần tìm kiếm" id="key" v-model="key">
                                        </div>
                                        <button type="submit" class="btn btn-primary" :disabled="key === '' || isSending === true">Tìm kiếm</button>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin" v-if="Object.keys(selectUser).length > 0 && selectUser.constructor === Object">
                <div class="card">
                    <div class="card-body">
                        <form class="form-sample">
                            <div class="row">
                                <div class="col-md-6">
                                    <form class="forms-sample">
                                        <div class="form-group">
                                            <label for="action_option">Username</label>
                                            <div class="font-weight-bold  mt-1">{{ selectUser.username }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="action_option">Tên</label>
                                            <div class="font-weight-bold  mt-1">{{ selectUser.name }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="action_option">Số dư tài khoản</label>
                                            <div class="font-weight-bold  mt-1">{{ selectUser.coin | currencyFormat }} Xu</div>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-6">
                                    <form class="forms-sample" @submit.prevent="submitAddOrSubCurrency">
                                        <div class="form-group">
                                            <label for="action_option">Thao tác</label>
                                            <select class="form-control" id="action_option" v-model="action_option">
                                                <option value="add">Cộng Xu</option>
                                                <option value="sub">Trừ Xu</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="currency" v-if="action_option == 'add'">Số xu cộng thêm</label>
                                            <label for="currency" v-else>Số xu bị trừ</label>
                                            <input type="number" class="form-control" placeholder="10,000 Xu" id="currency" :min="0" v-model="currency">
                                        </div>
                                        <button type="submit" class="btn btn-primary" :disabled="currency === '' || isSending === true">Gửi</button>
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Số dư tài khoản</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, index) in users.data" :key="index" @click="selectIsUser(index)">
                                    <td>{{ user.username }}</td>
                                    <td>{{ user.coin | currencyFormat }} Xu</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.phone_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="float-right">
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" :class="disabledPrevPageFlg == true ? 'disabled' : ''">
                                    <a class="page-link" aria-label="Previous" v-on:click="prePage()">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link">{{users.current_page}}</a>
                                </li>
                                <li class="page-item" :class="disabledNextPageFlg == true ? 'disabled' : ''">
                                    <a class="page-link" aria-label="Next" v-on:click="nextPage()">
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
    let NumberFormatEN = Intl.NumberFormat('en-US');
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                users: {},
                selectUser: {},
                action_option: 'add',
                currency: '',
                key: '',
                disabledPrevPageFlg: false,
                disabledNextPageFlg: false,
                isSending: false
            }
        },
        mounted() {
            this.getResults('/api/users')
        },
        methods: {
            prePage() {
                this.getResults(this.users.prev_page_url)
            },
            nextPage() {
                this.getResults(this.users.next_page_url)
            },
            getResults(page_url){
                this.axios
                .get(page_url)
                .then(response => {
                    this.users = response.data.result
                    if (response.data.result.prev_page_url == null) {
                        this.disabledPrevPageFlg = true
                    } else {
                        this.disabledPrevPageFlg = false
                    }
                    if (response.data.result.next_page_url == null) {
                        this.disabledNextPageFlg = true
                    } else {
                        this.disabledNextPageFlg = false
                    }
                })
                .catch(error => {
                    return
                });
            },
            selectIsUser(index) {
                this.selectUser = this.users.data[index]
            },
            submitAddOrSubCurrency(e) {
                this.isSending = true
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/payment/admin-add-or-sub-currenry-user', {
                    user_id: this.selectUser.id,
                    action_option: this.action_option,
                    currency: this.currency
                })
                .then((response) => {
                    this.isSending = false
                    if (response.data.result !== false) {
                        this.$store.dispatch('loadCurrency')
                        this.selectUser.coin = response.data.result

                        this.$notify({
                            group: 'foo',
                            title: 'Thao tác thành công.',
                            text,
                            duration: 10000,
                            speed: 1000
                        })

                        return
                    }
                    this.$notify({
                        group: 'foo',
                        title: 'Thao tác thất bại.',
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
            submitSearch(e) {
                this.isSending = true
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/users/search', {
                    key: this.key,
                })
                .then((response) => {
                    this.isSending = false
                    if (response.data.result !== null) {
                        this.selectUser = response.data.result
                        return
                    }
                    this.selectUser = {}
                    this.$notify({
                        group: 'foo',
                        title: 'Người dùng không được tìm thấy',
                        text,
                        duration: 10000,
                        speed: 1000
                    })
                })
                .catch((error) => {
                    this.isSending = false
                    this.$notify({
                        group: 'foo',
                        title: 'Người dùng không được tìm thấy',
                        text,
                        duration: 10000,
                        speed: 1000
                    })
                    return
                });
            }
        },
        filters: {
            currencyFormat(value) {
                if (value == null || value == '') {
                    value = 0
                }
                return NumberFormatEN.format(value)
            }
        }
    }
</script>