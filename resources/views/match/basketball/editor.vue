<template>
    <Modal v-model="visible" :loading="loading" width="800" :title="title"
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
                <Col span="11"  offset="2">
                    <FormItem label="客场" prop="awaysxname"
                              :rules="{required: true, message: '请输入客场名称', trigger: 'blur'}">
                        <Input v-model="formItem.awaysxname"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem label="期号" prop="bind">
                        <Input v-model="formItem.bind"/>
                    </FormItem>
                </Col>
                <Col span="11"  offset="2">
                    <FormItem label="日期" prop="date"
                              :rules="{required: true,  type: 'date', message: '请选择日期', trigger: 'blur'}">
                        <DatePicker type="date"  v-model="formItem.date"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11" >
                    <FormItem label="开赛时间" prop="beginning"
                              :rules="{required: true, type: 'date', message: '请选择比赛时间', trigger: 'blur'}">
                        <DatePicker type="datetime" v-model="formItem.beginning"/>
                    </FormItem>
                </Col>
                <Col span="11" offset="2">
                    <FormItem label="截止时间" prop="deadline"
                              :rules="{required: true,  type: 'date', message: '请选择截止时间', trigger: 'blur'}">
                        <DatePicker type="datetime" v-model="formItem.deadline"/>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <sf :item="formItem.odds.sf" editor @updated="update_sf"/>
                <dxf :item="formItem.odds.dxf" editor @updated="update_dxf"/>
                <rfsf :item="formItem.odds.rfsf" editor @updated="update_rfsf"/>
                <sfc :item="formItem.odds.sfc_z" editor @updated="update_sfc_z"
                     flag='sfc_z' item-color="error"/>
                <sfc :item="formItem.odds.sfc_k" editor @updated="update_sfc_k"
                     flag='sfc_k' item-color="success"/>
            </Row>
        </Form>
    </Modal>
</template>
<script>
    import util from '@/libs/dateUtil'
    import sf from './odds/sf';
    import dxf from './odds/dxf';
    import rfsf from './odds/rfsf';
    import sfc from './odds/sfc';

    export default {
        name: 'editor',
        components: {sf, dxf, rfsf, sfc},
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
                formItem:  {
                    id: '',
                    lg: '',
                    state: 1,
                    awaysxname: '',
                    homesxname: '',
                    bind: '',
                    date: '',
                    endtime: '',
                    beginning: '',
                    odds: {
                        sf: '',
                        rfsf: '',
                        dxf: '',
                        sfc_k: '',
                        sfc_z: '',
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
            update_sf(sf) {
                this.formItem.odds.sf = sf;
            },
            update_dxf(dxf) {
                this.formItem.odds.dxf = dxf;
            },
            update_rfsf(rfsf) {
                this.formItem.odds.rfsf = rfsf;
            },
            update_sfc_k(sfc_k) {
                this.formItem.odds.sfc_k = sfc_k;
            },
            update_sfc_z(sfc_z) {
                this.formItem.odds.sfc_z = sfc_z;
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
                                this.onCancel()
                            }, null,
                            () => this.resetLoading()
                        )
                    } else {
                        this.resetLoading()
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
                item.beginning = util.formatDateTime(this.formItem.beginning);
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
