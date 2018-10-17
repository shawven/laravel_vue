<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">胜负:</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-if="sfOdds.home" type="border" color="error">主：{{sfOdds.home}}</Tag>
                <Tag v-if="sfOdds.away" type="border" color="success">客：{{sfOdds.away}}</Tag>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" @on-ok="handleSubmit('formItem')" :loading="loading" :title="title" class="top-modal"
               width="400">
            <Form ref="formItem" :model="formItem" :label-width="50" inline>
                <Row>
                    <Col span="2" offset="2" style="padding-top: 3px">
                        <Tag color="error">主胜</Tag>
                    </Col>
                    <Col span="9">
                        <FormItem label="赔率" prop="home"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.home"/>
                        </FormItem>
                    </Col>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="success">客胜</Tag>
                    </Col>
                    <Col span="9">
                        <FormItem label="赔率" prop="away"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.away"/>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>
<script>
    export default {
        name: "sf",
        props: {
            editor: {
                type: Boolean,
                default: false
            },
            item: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                localItem: {away: null, home: null},
                formItem: {},
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? "添加胜负赔率" : "编辑胜负赔率"
            },
            buttonText() {
                return !this.item ? "添加" : "编辑"
            },
            buttonIcon() {
                return !this.item ? "plus" : "android-create"
            },
            buttonType() {
                return !this.item ? "info" : "warning"
            },
            sfOdds () {
                let odds = this.deepCopy(this.localItem);
                this.formItem = this.deepCopy(odds);
                if (!this.item) return odds;
                this.item.split(',', 2).forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let win = arr[0];
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    if (win === '2') {
                        odds.away = rate;
                    } else if (win === '1') {
                        odds.home = rate;
                    }
                });
                this.formItem = this.deepCopy(odds);
                return odds;
            }
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    this.resetLoading();
                    if (valid) {
                        let sf = '2|' + this.formItem.away.toFixed(2) + ',1|' + this.formItem.home.toFixed(2);
                        this.$emit('updated',sf);
                        this.modal = false;
                    }
                })
            },
            resetLoading() {
                this.loading = false;
                this.$nextTick(() => this.loading = true)
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    }
</script>
