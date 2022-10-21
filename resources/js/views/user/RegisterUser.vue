<template>
    <section class="section">
        <div class="container page-user mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand text-img">
                    SupportLive.Me
                    </div>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Đăng ký</h4>
                        </div>

                        <div class="card-body">
                            <form class="needs-validation" novalidate="" @submit.prevent="register">
                                <div class="form-group">
                                    <label for="email">Username</label>
                                    <input id="email" type="email" class="form-control" placeholder="Username" v-model="username" tabindex="1" required autofocus>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" placeholder="Email" v-model="email" tabindex="2" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Password</label>
                                    <input id="password" type="password" class="form-control" placeholder="Password" v-model="password" tabindex="3" required>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" tabindex="4" id="remember-me" checked required>
                                    <label class="custom-control-label" for="remember-me">Đồng ý Điều khoản & Điều kiện</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-check-label text-danger" v-if="message != ''">{{message}}</label>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Đăng ký</button>
                                </div>
                            </form>
                            <div class="text-center mt-4 mb-3">
                                <div class="text-job text-muted">
                                    Bạn có sẵn sàng để tạo một tài khoản? 
                                    <router-link :to="{name: 'login'}" class="text-primary font-weight-bold">Đăng nhập</router-link>
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
                username:'',
                email:'',
                password:'',
                message: '',
            }
        },
        methods: {
            register() {
                this.$store.dispatch('register', {
                    username: this.username,
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    this.$router.push({ name: 'login' })
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
                });
            }
        }
    }
</script>