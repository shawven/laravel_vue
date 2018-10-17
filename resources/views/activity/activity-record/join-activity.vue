<template>
    <Modal v-model="visible" width="500" title="参与活动" :loading="loading"
           @on-ok="handleSubmit" @on-cancel="cancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="16">
                    <FormItem label="用户" prop="user_id"
                              :rules="{required: true, type:'number', message: '请选择用户', trigger: 'blur'}">
                        <Input v-model="formItem.nickname" readonly/>
                    </FormItem>
                </Col>
                <Col span="6" offset="2">
                    <Button type="primary" icon="ios-search" @click="userSelector = true">选择用户</Button>
                </Col>
            </Row>
            <Row>
                <Col span="16">
                    <FormItem label="活动" prop="activity_id"
                              :rules="{required: true, type:'number', message: '请选择活动', trigger: 'blur'}">
                        <Input v-model="formItem.activityName" readonly/>
                    </FormItem>
                </Col>
                <Col span="6" offset="2">
                    <Button type="primary" icon="ios-search" @click="activitySelector = true">选择活动</Button>
                </Col>
            </Row>
        </Form>
        <user-selector v-model="userSelector" @select="selectUser"/>
        <activity-selector v-model="activitySelector" @select="selectActivity"/>
    </Modal>
</template>

<script>
    const userSelector = () => import('@/views/user/user-selector');
    const activitySelector = () => import('../activity-selector');

    export default {
        name: "join-activity",
        components: {userSelector, activitySelector},
        props: {
            url: String,
            value: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                userId: '',
                userSelector: false,
                activitySelector: false,
                visible: false,
                loading: true,
                formItem: {
                    user_id: '',
                    activity_id: '',
                    nickname: '',
                    activityName: ''
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                if (value) {
                    this.formItem = {};
                }
            }
        },
        methods: {
            selectUser(userInfo) {
                this.formItem.user_id = userInfo.id;
                this.formItem.nickname = userInfo.usernick;
            },
            selectActivity(activityInfo) {
                this.formItem.activity_id = activityInfo.id;
                this.formItem.activityName = activityInfo.name;
            },
            handleSubmit () {
                this.$refs.formItem.validate((valid) => {
                    if (valid) {
                        this.$messageHttp.post(this.url, this.formItem,
                            () => {
                                this.$emit('created');
                                this.cancel();
                            }, null,
                            () => this.resetLoading()
                        )
                    } else {
                        this.resetLoading();
                    }
                })
            },
            cancel() {
                this.visible = false;
                this.$refs.formItem.resetFields();
                this.$emit('input', false);
            },
            resetLoading() {
                this.loading = false;
                this.$nextTick(() => this.loading = true)
            },
        }
    }
</script>

<style scoped>

</style>
