<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Cài đặt "Số lượng"</h4>
                        <p class="card-description">Các cài đặt này chỉ định hai thông số <span class="note-color">nhỏ nhất (min)</span> và <span class="note-color">lớn nhất (max)</span> để thiết lập khoảng giá trị số lượng hợp lệ. Vui lòng nhập, nếu bạn muốn thay đổi.</p>
                        <form class="forms-sample d-block d-sm-flex align-items-center" @submit.prevent="amountSetting">
                            <input type="number" class="form-control mb-2 mr-sm-2" v-model="amount_min" placeholder="Min (example 1000)">
                            <input type="number" class="form-control mb-2 mr-sm-2" v-model="amount_max" placeholder="Max (example 10000)">
                            <button type="submit" class="btn btn-primary btn-icon-text mb-2 d-flex" :disabled="amount_min === '' && amount_max === ''">
                            <i class="typcn typcn-input-checked btn-icon-prepend"></i>
                            Lưu
                            </button>
                        </form>
                    </div>

                    <div class="card-body">
                        <h4 class="card-title">Cài đặt "Tốc độ"</h4>
                        <p class="card-description">Các cài đặt này chỉ định hai thông số <span class="note-color">nhỏ nhất (min)</span> và <span class="note-color">lớn nhất (max)</span> thể hiện bằng <span class="note-color">giây/lượt</span> để thiết lập khoảng giá trị tốc độ hợp lệ. Vui lòng nhập, nếu bạn muốn thay đổi.</p>
                        <form class="forms-sample d-block d-sm-flex align-items-center" @submit.prevent="speedSetting">
                            <input type="number" class="form-control mb-2 mr-sm-2" v-model="speed_min" placeholder="Min (example 0)">
                            <input type="number" class="form-control mb-2 mr-sm-2" v-model="speed_max" placeholder="Max (example 120)">
                            <button type="submit" class="btn btn-primary btn-icon-text mb-2 d-flex" :disabled="speed_min === '' && speed_max === ''">
                            <i class="typcn typcn-input-checked btn-icon-prepend"></i>
                            Lưu
                            </button>
                        </form>
                    </div>

                    <div class="card-body">
                        <h4 class="card-title">Cài đặt "Kênh"</h4>
                        <p class="card-description">Các cài đặt này chỉ định thông số <span class="note-color">tên</span>, <span class="note-color">giá</span> và <span class="note-color">thông tin chi tiết</span> để thiết lập danh sách các Kênh. Bạn có thể tạo/chỉnh sửa/xóa.</p>
                        <form class="forms-sample" @submit.prevent="channelSetting">
                            <select class="form-control mb-2" v-model="channel_type" >
                                <option value="comment">Buff Comment Facebook</option>
                                <option value="share">Buff Share Facebook</option>
                            </select>
                            <input type="text" class="form-control mb-2 mr-sm-2" v-model="channel_name" placeholder="Tên">
                            <input type="text" class="form-control mb-2 mr-sm-2" v-model="channel_price" placeholder="Giá">
                            <textarea rows="4" class="form-control mb-2 mr-sm-2" v-model="channel_description" placeholder="Thông tin chi tiết"></textarea>
                            <button type="submit" class="btn btn-primary btn-icon-text mb-2" :disabled="channel_name === '' && channel_price === '' && channel_description === ''">
                            <i class="typcn typcn-input-checked btn-icon-prepend"></i>
                            Lưu
                            </button>
                        </form>
                    </div>

                    <div class="card-body">
                        <p class="card-description">
                            Danh sách kênh
                        </p>
                        <div class="table-borderless table-hover">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên kênh</th>
                                        <th>Loại kênh</th>
                                        <th>Giá</th>
                                        <th>Thông tin chi tiết</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody v-for="(channel, index) in items" :key="index">
                                    <tr>
                                        <td>{{ channel.channel_name }}</td>
                                        <td>{{ channel.channel_type === 'share' ? 'Buff Share Facebook' : 'Buff Comment Facebook' }}</td>
                                        <td>{{ channel.channel_price }}</td>
                                        <td style="white-space: pre-wrap; line-height: 2;">{{ channel.channel_description }}</td>
                                        <td>
                                            <div class="font-weight-bold mt-1" v-bind:style="{color: '#FF1E00', cursor: 'pointer'}" v-on:click="deleteChannel(index)"><i class="fa-solid fa-trash-can"></i></div>
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
                channel_type: 'comment',
                channel_name: '',
                channel_price: '',
                channel_description: '',
                channel_list: [],
                amount_min:'',
                amount_max:'',
                speed_min:'',
                speed_max:'',
            }
        },
        mounted() {
            this.axios
            .get('/api/facebook/settings')
            .then(response => {
                for (let data of response.data.result) {
                    if (data['type'] === 'channel') {
                        this.channel_list.push(data)
                    }
                    if (data['type'] === 'amount') {
                        this.amount_min = data['amount_min']
                        this.amount_max = data['amount_max']
                    }
                    if (data['type'] === 'speed') {
                        this.speed_min = data['speed_min']
                        this.speed_max = data['speed_max']
                    }
                }
                this.channel_list.reverse();
            })
            .catch(error => {
                console.log(error)
                return
            });
        },
        computed: {
            items() {
                return this.channel_list.reverse()
            }
        },
        methods: {
            amountSetting() {
                axios.post('/api/facebook/settings', {
                    amount_min: this.amount_min,
                    amount_max: this.amount_max,
                    type: 'amount'
                })
                .then((response) => {
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

            speedSetting() {
                axios.post('/api/facebook/settings', {
                    speed_min: this.speed_min,
                    speed_max: this.speed_max,
                    type: 'speed'
                })
                .then((response) => {
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

            channelSetting() {
                axios.post('/api/facebook/settings', {
                    channel_name: this.channel_name,
                    channel_price: this.channel_price,
                    channel_description: this.channel_description,
                    channel_type: this.channel_type,
                    type: 'channel'
                })
                .then((response) => {
                    if (response.data.delete === undefined) {
                        this.channel_list.push({
                            'channel_type': this.channel_type,
                            'channel_name': this.channel_name,
                            'channel_price': this.channel_price,
                            'channel_description': this.channel_description,
                        })
                        this.channel_list.reverse();

                        this.channel_type = 'comment'
                        this.channel_name = ''
                        this.channel_price = ''
                        this.channel_description = ''
                    }

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

            deleteChannel(index) {
                axios
                .post('/api/facebook/settings', {
                    id: this.channel_list[index].id,
                    channel_name: this.channel_list[index].channel_name,
                    action: 'delete',
                    type: 'channel'
                })
                .then((response) => {
                    this.channel_list.splice(index, 1)
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