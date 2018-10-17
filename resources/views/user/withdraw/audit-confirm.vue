<template>
    <Modal v-model="visible" @on-cancel="onCancel" width="300">
        <p slot="header" style="color:#f60;text-align:center">
            <Icon type="information-circled"></Icon>
            <span>审核确认</span>
        </p>
        <p class="font-sm font-weight-bold mt-2">本次提现金额：{{item.money}} ￥</p>
        <div slot="footer" class="text-center">
            <Button type="primary" size="large" :loading="loading1" @click="update(1)" class="mx-2">通过</Button>
            <Button type="error" size="large" :loading="loading2" @click="update(2)" class="mx-2">拒绝</Button>
        </div>
    </Modal>
</template>

<script>
    export default {
        name: "audit-confirm",
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
               loading1: false,
               loading2: false,
               visible: this.value
           }
        },
        watch: {
            value(value) {
                this.visible = value;
            }
        },
        methods: {
            update(value) {
                this.startLoading(value);
                this.$messageHttp.put('/api/users/withdraws/' + this.item.id, {audit_state: value},
                    () => {
                        this.$emit('updated', {...this.item, audit_state: value});
                        this.onCancel()
                    }, null,
                    () => this.stopLoading(value)
                )
            },
            startLoading(value) {
                if (value === 1) {
                    this.loading1 = true;
                } else {
                    this.loading2 = true;
                }
            },
            stopLoading(value) {
                if (value === 1) {
                    this.loading1 = false;
                } else {
                    this.loading2 = false;
                }
            },
            onCancel() {
                this.visible = false;
                this.$emit('input', false);
            },
        }
    }
</script>

<style scoped>

</style>
