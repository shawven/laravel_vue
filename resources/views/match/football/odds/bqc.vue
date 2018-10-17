<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">半全场</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-for="(item, index) in bqcOdds" v-if="item.rate" :key="index" type="border" :color="item.color">
                    {{item.text}}&nbsp;&nbsp;{{item.rate}}
                </Tag>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" @on-ok="handleSubmit('formItem')" :loading="loading" :title="title"
               class="top-modal" width="600">
            <Form ref="formItem" :model="formItem" inline :lable-width="100">
                <Row>
                    <Col span="8" v-for="(item, index) in formItem.items " :key="index">
                        <Tag type="dot" :color="item.color">{{item.text}}:</Tag>
                        <FormItem :prop="'items.' + index + '.rate'"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :min="0" :step="1" v-model="item.rate"/>
                        </FormItem>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>
<script>
    export default {
        name: "bqc",
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
                localItem: {
                    items: [
                        {text: '胜胜', color:"error", score:"33", rate: null},
                        {text: '胜平', color:"error", score:"31", rate: null},
                        {text: '胜负', color:"gray", score:"30", rate: null},
                        {text: '平胜', color:"error", score:"13", rate: null},
                        {text: '平平', color:"gray", score:"11", rate: null},
                        {text: '平负', color:"success", score:"10", rate: null},
                        {text: '负胜', color:"gray", score:"03", rate: null},
                        {text: '负平', color:"success", score:"01", rate: null},
                        {text: '负负', color:"success", score:"00", rate: null}
                    ]
                },
                formItem: {},
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? "添加半全场赔率": '修改半全场赔率'
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
            bqcOdds () {
                let odds = this.deepCopy(this.localItem.items);
                this.formItem.items = this.deepCopy(odds);
                if (!this.item) return odds;
                this.item.split(',', 9).forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let score = arr[0];
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    Object.keys(odds).forEach((key) => {
                        if (odds[key].score === score) {
                            odds[key].rate = rate;
                        }
                    })
                });
                this.formItem.items = this.deepCopy(odds);
                return odds;
            }
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    this.resetLoading();
                    if (valid) {
                        let bqc = '3|' + this.formItem.draw.toFixed(2)
                            +  ',1|' + this.formItem.away.toFixed(2) + ',0|' + this.formItem.home.toFixed(2) ;
                        this.$emit('updated',bqc);
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
