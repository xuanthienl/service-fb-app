<template>
    <div class="content-wrapper service-facebook">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">THÔNG TIN CÁ NHÂN</h4>
                        <form class="forms-sample" @submit.prevent="updateProfile">
                            <div class="form-group">
                                <input class="form-control" type="text" v-model="users.name"  placeholder="your name.">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" v-model="users.username" placeholder="your username.">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" v-model="users.email" placeholder="your email.">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" v-model="users.phone_number" placeholder="your phone number.">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" v-model="users.address" placeholder="your address.">
                            </div>
                            <div class="form-group" v-if="changePassword">
                                <input class="form-control" type="password" v-model="users.old_password"  placeholder="your old password.">
                            </div>
                            <div class="form-group" v-if="changePassword">
                                <input class="form-control" type="password" v-model="users.password"  placeholder="your new password.">
                            </div>
                            <div class="form-group" v-if="changePassword">
                                <input class="form-control" type="password" v-model="users.password_confirmation"  placeholder="your password confirmation.">
                            </div>
                            <div class="form-group" v-if="message != ''">
                                <p class="text-message rounded-pill">{{message}}</p>
                            </div>
                            <p class="card-description" style="color:#68b0ab; cursor: pointer; text-decoration: underline;"  v-on:click="changePassword ? changePassword = false : changePassword = true">Thay đổi mật khẩu</p>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    export default {
        metaInfo () {
            return {
                title: this.users.name != '' ? this.users.name + ' (@' + this.users.username + ') / Supportlive.Az' : '@'+this.users.username + ' / ʟ ᴏ ɴ ᴇ ʟ ʏ',
            }
        },
        data() {
            return {
                user:this.$store.getters.getUser,
                users: {
                    id: '',
                    email: '',
                    username: '',
                    name: '',
                    phone_number: '',
                    address: '',
                    coin: '',
                    old_password: '',
                    password: '',
                    password_confirmation: ''
                },
                message: '',
                changePassword: false,
            }
        },
        mounted() {
            axios
            .get('/api/user/' + this.$route.params.username)
            .then(response => {
                this.users.id = response.data.user.id
                this.users.email = response.data.user.email
                this.users.username = response.data.user.username
                this.users.name = response.data.user.name != null ? response.data.user.name : ''
                this.users.phone_number = response.data.user.phone_number
                this.users.address = response.data.user.address != null ? response.data.user.address : ''
                this.users.coin = response.data.user.coin
                this.users.roles = response.data.user.roles
            })
            .catch(error => {
                this.$router.back()
            });
        },
        watch: {
            $route(to) {
                axios
                .get('/api/user/'+ to.params.username)
                .then(response => {
                    this.users.id = response.data.user.id
                    this.users.email = response.data.user.email
                    this.users.username = response.data.user.username
                    this.users.name = response.data.user.name != null ? response.data.user.name : ''
                    this.users.phone_number = response.data.user.phone_number
                    this.users.address = response.data.user.address != null ? response.data.user.address : ''
                    this.users.coin = response.data.user.coin
                    this.users.roles = response.data.user.roles
                })
                .catch(error => {
                    this.$router.back()
                });
            }
        },
        methods: {
            updateProfile(e) {
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/user/'+this.users.id+'/profile/update', {
                    data: this.users
                })
                .then((response) => {
                    this.$store.dispatch('reloadUser', this.users)
                    this.$notify({
                        group: 'foo',
                        title: 'Cập nhập thông tin cá nhân thành công',
                        duration: 10000,
                        speed: 1000
                    });

                    this.$router.push({ name: 'setting-user', params: { username: this.users.username } })
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        console.log(error);
                    }
                    return
                });
            }
        },
    }
</script>