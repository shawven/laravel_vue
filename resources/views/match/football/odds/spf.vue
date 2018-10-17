<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">{{tagText}}</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <span class="expand-value">
                    <Tag v-if="spfOdds.home" type="border" color="error">主：{{spfOdds.home}}</Tag>
                    <Tag v-if="spfOdds.draw" type="border" color="gray">平：{{spfOdds.draw}}</Tag>
                    <Tag v-if="spfOdds.away" type="border" color="success">客：{{spfOdds.away}}</Tag>
                     <Tag v-if="!isNspf" type="border">{{rq}}</Tag>
                </span>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" @on-ok="handleSubmit('formItem')" :loading="loading" :title="title"
               class="top-modal" width="600">
            <Form ref="formItem" :model="formItem" :label-width="50" inline>
                <Row v-if="!isNspf">
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="success">让球</Tag>
                    </Col>
                    <Col span="6">
                        <FormItem label="数量" prop="rq"
                                  :rules="{required: true, type: 'number', message: '请填写让球个数', trigger: 'blur'}">
                            <InputNumber :max="10" :min="-10" :step="1" v-model="formItem.rq"/>
                        </FormItem>
                    </Col>
                </Row>
                <Row>
                    <Col span="2"  style="padding-top: 3px">
                        <Tag color="error">主胜</Tag>
                    </Col>
                    <Col span="6">
                        <FormItem label="赔率" prop="home"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.home"/>
                        </FormItem>
                    </Col>
                    <Col span="2" style="padding-top: 3px">
                        <Tag>平局</Tag>
                    </Col>
                    <Col span="6">
                        <FormItem label="赔率" prop="draw"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.draw"/>
                        </FormItem>
                    </Col>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="success">客胜</Tag>
                    </Col>
                    <Col span="6">
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
        name: "spf",
        props: {
            flag: {
                type: String,
                default: 'spf'
            },
            editor: {
                type: Boolean,
                default: false
            },
            item: {
                type: String,
                default: ''
            },
            rq: null
        },
        data() {
            return {
                localItem: {rq: this.rq, away: null, draw: null, home: null},
                formItem: {},
                isNspf: this.flag === 'nspf',
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item
                    ? (this.isNspf ? "添加胜平负赔率" : '添加让球胜平负赔率')
                    : (this.isNspf ? "编辑胜平负赔率" : '修改让球胜平负赔率')
            },
            tagText() {
                return this.isNspf ? "胜平负" : '让球胜平负';
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
            spfOdds () {
                this.localItem.rq = +this.rq;
                let odds = this.deepCopy(this.localItem);
                this.formItem = this.deepCopy(odds);
                if (!this.item) return odds;
                this.item.split(',', 3).forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let win = arr[0];
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    if (win === '0') {
                        odds.away = rate;
                    } else if (win === '1') {
                        odds.draw = rate;
                    } else if (win === '3') {
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
                        let spf = '3|' + this.formItem.home.toFixed(2)
                            +  ',1|' + this.formItem.away.toFixed(2) + ',0|' + this.formItem.draw.toFixed(2) ;
                        this.$emit('updated',{spf, rq:this.formItem.rq});
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
