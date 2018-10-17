<template>
    <Modal v-model="visible" :loading="loading" width="750" :title="title"
           @on-ok="handleSubmit('formItem')" @on-cancel="onCancel">
        <Form ref="formItem" :model="formItem" :label-width="70">
            <Row>
                <Col span="11">
                    <FormItem label="联赛名" prop="lg"
                              :rules="{required: true, message: '请输入联赛名称', trigger: 'blur'}">
                        <Input v-model="formItem.lg" />
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="状态" prop="state">
                        <RadioGroup v-model="formItem.state">
                            <Radio :label="1"><Tag color="success">有效</Tag></Radio>
                            <Radio :label="0"><Tag>无效</Tag></Radio>
                        </RadioGroup>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="主场" prop="homesxname"
                              :rules="{required: true, message: '请输入主场名称', trigger: 'blur'}">
                        <Input v-model="formItem.homesxname"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="客场" prop="awaysxname"
                              :rules="{required: true, message: '请输入客场名称', trigger: 'blur'}">
                        <Input v-model="formItem.awaysxname"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="期号" prop="bind"
                              :rules="{required: true, message: '请输入期号', trigger: 'blur'}">
                        <Input v-model="formItem.bind"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="日期" prop="date"
                              :rules="{required: true,  type: 'date', message: '请选择日期', trigger: 'blur'}">
                        <DatePicker type="date"  v-model="formItem.date"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11" >
                    <FormItem label="开赛时间" prop="matchtime"
                              :rules="{required: true, type: 'date', message: '请选择比赛时间', trigger: 'blur'}">
                        <DatePicker type="datetime" v-model="formItem.matchtime"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="截止时间" prop="endtime"
                              :rules="{required: true,  type: 'date', message: '请选择最后日期', trigger: 'blur'}">
                        <DatePicker type="datetime" v-model="formItem.endtime" />
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <spf :item="formItem.odds.spf" editor @updated="update_spf" flag="spf"
                     class="mb-3" :rq="formItem.odds.rq"/>
                <spf :item="formItem.odds.nspf" editor @updated="update_nspf" flag="nspf"
                     class="my-3"/>
                <jqs :item="formItem.odds.jqs" editor @updated="update_jqs" class="my-3"/>
                <bqc :item="formItem.odds.bqc" editor @updated="update_bqc" class="my-3"/>
                <bf :item="formItem.odds.bf" editor @updated="update_bf" class="mt-3"/>
            </Row>
        </Form>
    </Modal>
</template>
<script>
    import util from '@/libs/dateUtil'
    import spf from './odds/spf';
    import jqs from './odds/jqs';
    import bf from './odds/bf';
    import bqc from './odds/bqc';

    export default {
        name: 'editor',
        components: {spf, jqs, bf, bqc},
        props: {
            url: String,
            title: String,
            edit: {
                type: Boolean,
                default: true
            },
            item: {
                type: Object,
                default() {
                    return {}
                }
            },
            value: {
                type: Boolean,
                default: false
            },
        },
        data () {
            return {
                loading: true,
                visible: this.value,
                formItem:{
                    id: '',
                    match_id: '',
                    bind: '',
                    homesxname: '',
                    awaysxname: '',
                    lg: '',
                    play: '',
                    date: '',
                    addtime: '',
                    updatetime: '',
                    odds: {
                        rq: '',
                        nspf: '',
                        spf: '',
                        bf: '',
                        jqs: '',
                        bqc: '',
                    }
                }
            }
        },
        watch: {
            value(value) {
                this.visible = value;
                this.formItem = value && this.edit ? this.deepCopy(this.item) : {odds:{}}
            }
        },
        methods: {
            update_spf({spf, rq}) {
                this.formItem.odds.spf = spf;
                this.formItem.odds.rq = rq;
            },
            update_nspf(nspf) {
                this.formItem.odds.nspf = nspf;
            },
            update_jqs(jqs) {
                this.formItem.odds.jqs = jqs;
            },
            update_bqc(bqc) {
                this.formItem.odds.bqc = bqc;
            },
            update_bf(bf) {
                this.formItem.odds.bf = bf;
            },
            handleSubmit (name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        let url = this.edit ? this.url + '/' + this.formItem.id : this.url;
                        let request = this.edit ? this.$messageHttp.put : this.$messageHttp.post;
                        let item = this.getTransformItem();
                        request(url, item,
                            () => {
                                if (this.edit) {
                                    this.$emit('updated', this.deepCopy({...this.item, ...item}))
                                } else {
                                    this.$emit('created');
                                }
                                this.onCancel();
                            }, null,
                            () => this.resetLoading()
                        )
                    } else {
                        this.resetLoading();
                    }
                })
            },
            onCancel() {
                this.visible = false;
                this.$refs.formItem.resetFields();
                this.$emit('input', false);
            },
            getTransformItem() {
                let item = JSON.parse(JSON.stringify(this.formItem));
                item.date = util.formatDate(this.formItem.date);
                item.endtime = util.formatDateTime(this.formItem.endtime);
                item.matchtime = util.formatDateTime(this.formItem.matchtime);
                return item;
            },
            resetLoading() {
                this.loading = false;
                this.$nextTick(() => this.loading = true)
            },
            invalid(str) {
                return this.formItem.odds[str] === undefined || this.formItem.odds[str] === null || this.formItem.odds[str].trim() === ''
            },
            deepCopy(obj) {
                return JSON.parse(JSON.stringify(obj))
            }
        }
    }
</script>
