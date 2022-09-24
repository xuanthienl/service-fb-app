<template>
    <div class="content-wrapper service-facebook">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="card-description">Vui lòng điền các mục cần thiết và nội dung yêu cầu của bạn từ mẫu yêu cầu.</p>
                        <p class="card-description">Sau khi xác nhận nội dung yêu cầu của bạn, người phụ trách sẽ liên hệ với bạn theo địa chỉ email hoặc số điện thoại của bạn.</p>
                        <form class="forms-sample" @submit.prevent="submitRequestContact">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Email" id="uid" v-model="email">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Phone number" id="uid" v-model="phone">
                            </div>
                            <div class="form-group">
						        <textarea class="form-control" id="content" rows="4" placeholder="Nội dung yêu cầu của bạn" v-model="content"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" :disabled="email === '' || phone === '' || content === '' || isSending === true">Gửi</button>
                        </form>
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
    export default {
        data() {
            return {
                email: '',
                phone: '',
                content: '',
                isSending: false
            }
        },
        methods: {
            submitRequestContact(e) {
                this.isSending = true
                e.preventDefault();
                axios.post('/api/contact', {
                    email: this.email,
                    phone: this.phone,
                    content: this.content
                })
                .then((response) => {
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
                    this.isSending = false
                    return
                });
            },
        }
    }
</script>