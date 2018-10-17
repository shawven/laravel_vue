<template>
    <Modal v-model="visible" @on-cancel="onCancel" width="300">
        <p slot="header" style="color:#f60;text-align:center">
            <Icon type="information-circled"></Icon>
            <span>提现确认</span>
        </p>
        <p class="font-sm font-weight-bold mt-2">本次提现金额：{{item.money}} ￥</p>
        <div slot="footer" class="text-center">
            <Button type="primary" size="large" :loading="loading" @click="accept" class="mx-2">同意</Button>
            <Button type="error" size="large"  @click="reject" class="mx-2">拒绝</Button>
        </div>
    </Modal>
</template>

<script>
    export default {
        name: "withdraw-confirm",
        props: {
            title: String,
            item: Object,
            value: {
                type: Boolean,
                default: false
            }
        },
        data() {
           return {
               loading: false,
               visible: this.value
           }
        },
        watch: {
            value(value) {
                this.visible = value;
            }
        },
        methods: {
            accept() {
                this.loading = true;
                this.$messageHttp.put('/api/users/withdraws/' + this.item.id, {state: 1},
                    () => {
                        this.$emit('updated', {... this.item, state: 1});
                        this.onCancel()
                    }, null,
                    () => this.loading = false
                )
            },
            reject() {
                this.$modalHttp.prompt('/api/users/withdraws/' + this.item.id, {state: 2},
                    'reason', '拒绝提现', '请输入拒绝的原因已告知用户',
                    () => {
                        this.$emit('updated', {... this.item, state: 1});
                        this.onCancel()
                    }
                )
            },
            onCancel() {
                this.visible = false;
                this.$emit('input', false);
            }
        }
    }
</script>

<style scoped>

</style>
