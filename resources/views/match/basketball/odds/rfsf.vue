<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">让分胜负: </Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-if="rfsfOdds.home.diff || rfsfOdds.home.rate" type="border" color="error">
                    主：{{rfsfOdds.home.diff}}&nbsp;&nbsp;{{rfsfOdds.home.rate}}
                </Tag>
                <Tag v-if="rfsfOdds.away.diff || rfsfOdds.away.rate" type="border" color="success">
                    客：{{rfsfOdds.away.diff}}&nbsp;&nbsp;{{rfsfOdds.away.rate}}
                </Tag>
            </Col>
            <Col span="2" v-if="editor" >
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" @on-ok="handleSubmit('formItem')" :loading="loading" :title="title"
               width="350" class="top-modal">
            <Form ref="formItem" :model="formItem" :label-width="50" inline>
                <Row>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="error">主胜</Tag>
                    </Col>
                    <Col span="11">
                        <FormItem label="让分" prop="home.diff"
                                  :rules="{required: true,  message: '请填写让分', trigger: 'blur'}">
                            <Input v-model="formItem.home.diff"/>
                        </FormItem>
                    </Col>
                    <Col span="11">
                        <FormItem label="赔率" prop="home.rate"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.home.rate"/>
                        </FormItem>
                    </Col>
                    <Col span="2" style="padding-top: 3px">
                        <Tag color="success">客胜</Tag>
                    </Col>
                    <Col span="11">
                        <FormItem label="让分" prop="away.diff"
                                  :rules="{required: true, message: '请填写让分', trigger: 'blur'}">
                            <Input v-model="formItem.away.diff"/>
                        </FormItem>
                    </Col>
                    <Col span="11">
                        <FormItem label="赔率" prop="away.rate"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :max="10" :min="0" :step="0.01" v-model="formItem.away.rate"/>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>
<script>
    export default {
        name: "rfsf",
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
                localItem: {away: {diff: null, rate: null}, home: {diff: null, rate: null}},
                formItem: {},
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? "添加让分胜负赔率" : "编辑让分胜负赔率"
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
            rfsfOdds () {
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
                        odds.away = {diff, rate}
                    } else if ((win === '1')){
                        odds.home = {diff, rate}
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
                        let rfsf = '2|' + this.formItem.away.diff + '|' + this.formItem.away.rate.toFixed(2)
                            + ',1|' + this.formItem.home.diff + '|' + this.formItem.home.rate.toFixed(2);
                        this.$emit('updated', rfsf);
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
