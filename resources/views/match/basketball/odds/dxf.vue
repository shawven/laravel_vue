<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">大小分:</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-if="dxfOdds.large.diff || dxfOdds.large.rate" type="border" color="error">
                    {{dxfOdds.large.diff}}&nbsp;&nbsp;{{dxfOdds.large.rate}}
                </Tag>
                <Tag v-if="dxfOdds.small.diff || dxfOdds.small.rate" type="border" color="success">
                    {{dxfOdds.small.diff}}&nbsp;&nbsp;{{dxfOdds.small.rate}}
                </Tag>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" @on-ok="handleSubmit('formItem')" :loading="loading" :title="title"
               width="350"  class="top-modal">
            <Form ref="formItem" :model="formItem" :label-width="50" inline>
                <Row>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="error">主胜</Tag>
                    </Col>
                    <Col span="11">
                        <FormItem label="分数" prop="large.diff"
                                  :rules="{required: true, message: '请填写大小分', trigger: 'blur'}">
                            <Input v-model="formItem.large.diff"/>
                        </FormItem>
                    </Col>
                    <Col span="11">
                        <FormItem label="赔率" prop="large.rate"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.large.rate"/>
                        </FormItem>
                    </Col>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="success">客胜</Tag>
                    </Col>
                    <Col span="11">
                        <FormItem label="分数" prop="small.diff"
                                  :rules="{required: true, message: '请填写大小分', trigger: 'blur'}">
                            <Input v-model="formItem.small.diff"/>
                        </FormItem>
                    </Col>
                    <Col span="11">
                        <FormItem label="赔率" prop="small.rate"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.small.rate"/>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>
<script>
    export default {
        name: "dxf",
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
                localItem: {small: {diff:null, rate: null}, large: {diff:null, rate: null}},
                formItem: {},
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? "添加大小分赔率" : "编辑大小分赔率"
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
            dxfOdds () {
                let odds = this.deepCopy(this.localItem);
                this.formItem = this.deepCopy(odds);
                if (!this.item) return odds;
                this.item.split(',', 3).forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 3);
                    let win = arr[0];
                    let diff =  arr[1];
                    let rate = isNaN(Number.parseFloat(arr[2])) ? null : Number.parseFloat(arr[2]);
                    if (win === '2') {
                        odds.small = {diff, rate}
                    } else if ((win === '1')){
                        odds.large = {diff, rate}
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
                        let dxf = '2|' + this.formItem.small.diff + '|' + this.formItem.small.rate.toFixed(2)
                            + ',1|' + this.formItem.large.diff + '|' + this.formItem.large.rate.toFixed(2);
                        this.$emit('updated',dxf);
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
