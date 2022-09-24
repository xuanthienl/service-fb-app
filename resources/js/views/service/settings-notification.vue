<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Cài đặt thông báo</h4>
                        <p class="card-description">Các cài đặt này chỉ định <span class="note-color">Telegram Bot</span> với <span class="note-color">HTTP API</span> có định dạng <span class="note-color">5423286618:AAHLl19QC8u-B9NQmGWJ9B0zC61aPNypeZ8</span>. Vui lòng nhập để thiết lập hoặc nếu muốn thay đổi.</p>
                        <p class="card-description">Các bước tạo Telegram Bot</p>
                        <p class="card-description">B1: Nhập @Botfather vào tab tìm kiếm và chọn bot này.</p>
                        <p class="card-description">B2: Chọn hoặc nhập /newbot lệnh và gửi nó. Sau đó hãy làm theo hướng dẫn.</p>
                        <p class="card-description">B3: Thêm bot vừa tạo vào nhóm hoặc kênh Telegram và thăng cấp với tư cách là quản trị viên.</p>
                        <p class="card-description">B4: Nhóm hoặc kênh Telegram trên cần phải đang hoạt động (tương tác) tại thời điểm thiết lập Bot. Hãy add một messages/post nếu quá trình thiết lập chưa thành công.</p>
                        <form class="forms-sample d-block d-sm-flex" @submit.prevent="telegramBotSetting">
                            <input type="text" class="form-control mb-2 mr-sm-2" v-model="telegram_bot_token" placeholder="Nhập token HTTP API">
                            <button type="submit" class="btn btn-primary btn-icon-text mb-2 d-flex" :disabled="telegram_bot_token === '' || isSending === true">
                            <i class="typcn typcn-input-checked btn-icon-prepend"></i>
                            Lưu
                            </button>
                        </form>
                    </div>

                    <div class="card-body">
                        <p class="card-description">Bạn đã thiết lập thành công các cổng thông báo Telegram dưới đây. Khi có đơn được tạo, hệ thống sẽ gửi thông báo đến các cổng này. (dữ liệu lưu tại thời điểm thiết lập Bot)</p>
                        <div class="table-borderless table-hover">
                            <table class="table">
                                <tbody v-for="(notification, index) in notificationList" :key="index">
                                    <tr v-if="notification.bot_name !== null">
                                        <td>
                                            Tên Telegram Bot
                                            <div class="font-weight-bold mt-1" v-bind:style="{color: '#F7A76C'}">{{ notification.bot_name }}</div>
                                        </td>
                                        <td>
                                            Địa chỉ
                                            <div class="font-weight-bold mt-1">{{ notification.chat_title }} {{ notification.chat_type | testAddressFormat }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold mt-1" v-bind:style="{color: '#FF1E00', cursor: 'pointer'}" v-on:click="telegramDeleteBotSetting(index)"><i class="fa-solid fa-trash-can"></i></div>
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
    export default {
        data() {
            return {
                telegram_bot_token: '',
                notificationList: [],
                isSending: false
            }
        },
        mounted() {
            this.axios
            .get('/api/system-settings/index')
            .then(response => {
                this.notificationList = response.data.result
            })
            .catch(error => {
                console.log(error)
                return
            });
        },
        methods: {
            telegramBotSetting() {
                this.isSending = true
                axios.post('/api/system-settings/telegramBot-settings', {
                    bot_token: this.telegram_bot_token,
                })
                .then((response) => {
                    // for (var key in response.data.result) {
                    //     this.notificationList.push(response.data.result[key])
                    // }

                    // var arr = []
                    // A: for (var key in this.notificationList) {
                    //     if (arr.length === 0) {
                    //         arr.push(this.notificationList[key])
                    //         continue
                    //     }
                    //     for (var key2 in arr) {
                    //         if (arr[key2].chat_id == this.notificationList[key].chat_id && arr[key2].bot_name == this.notificationList[key].bot_name) {
                    //             continue A
                    //         }
                    //     }
                    //     arr.push(this.notificationList[key])
                    // }

                    // this.notificationList = arr

                    this.notificationList = response.data.result

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
            telegramDeleteBotSetting(index) {
                axios
                .post('/api/system-settings/telegramBot-settings/' + this.notificationList[index].id + '/delete')
                .then((response) => {
                    this.notificationList.splice(index, 1)
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
        },
        filters: {
            testAddressFormat(value) {
                return '(' + value + ')'
            },
        }
    }
</script>