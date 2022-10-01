<template>
    <section class="section">
        <div class="container page-user mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand text-img">
                    Supportlive.Az
                    </div>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 v-if="!forgotPassword">Đăng nhập</h4>
                            <h4 v-else>Cấp lại mật khẩu mới</h4>
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate="" @submit.prevent="login" v-if="!forgotPassword">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" placeholder="Email" v-model="email" tabindex="1" required autofocus>
                                </div>

                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">Password</label>
                                        <div class="float-right">
                                            <a v-on:click="forgotPassword = true, message = ''" class="text-small">Quên mật khẩu?</a>
                                        </div>
                                    </div>
                                    <input id="password" type="password" class="form-control" placeholder="Password" v-model="password" tabindex="2" required>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                    <input type="checkbox" v-model="remember_me" class="custom-control-input" tabindex="3" id="remember-me">
                                    <label class="custom-control-label" for="remember-me">Giữ cho tôi đăng nhập</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-check-label text-danger" v-if="message != ''">{{message}}</label>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Đăng nhập</button>
                                </div>
                            </form>

                            <form class="needs-validation" novalidate="" @submit.prevent="resetPassword" v-else>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" placeholder="Email" v-model="email" tabindex="1" required autofocus>
                                </div>
                                <div class="form-group">
                                    <label class="form-check-label text-primary" v-if="message != ''">{{message}}</label>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Cấp lại mật khẩu</button>
                                </div>
                            </form>
                            <div class="text-center mt-4 mb-3">
                                <div class="text-job text-muted" v-if="!forgotPassword">
                                    Không có tài khoản? 
                                    <router-link :to="{name: 'register'}" class="text-primary font-weight-bold">Đăng ký</router-link>
                                </div>
                                <div class="text-job text-muted" v-else>
                                    Bạn có sẵn một tài khoản?
                                    <a v-on:click="forgotPassword = false, message = ''" class="text-primary font-weight-bold">Đăng nhập</a>
                                </div>
                            </div>
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
                email:'',
                password:'',
                remember_me:'',
                message: '',
                forgotPassword: false,
            }
        },
        methods: {
            login() {
                this.$store.dispatch('retrieveToken', {
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    window.location.href = window.location.href
                    this.$router.push({ name: 'home' }).catch(()=>{});
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        return
                    }
                });
            },
            resetPassword() {
                axios.post('/api/reset-pasword', {
                    email: this.email,
                })
                .then((response) => {
                    this.forgotPassword = false
                    this.message = ''
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        return
                    }
                });
            },
        }
    }
</script>