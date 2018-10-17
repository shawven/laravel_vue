<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">进球数</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-for="(item, index) in jqsOdds" :key="index" type="border">
                    {{item.score}}分&nbsp;&nbsp;{{item.rate}}
                </Tag>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" :loading="loading" :title="title" @on-ok="handleSubmit('formItem')"
               class="top-modal">
            <Form ref="formItem" :model="formItem" inline>
                <Row v-for="(item, index) in formItem.items" :key="index">
                    <Col span="10">
                        <Tag type="dot" :color="itemColor">进球:</Tag>
                        <FormItem :prop="'items.' + index + '.score'"
                                  :rules="{required: true, type: 'number', message: '请填写进球数', trigger: 'blur'}">
                            <InputNumber :min="0" :step="1" v-model="item.score"/>
                        </FormItem>
                    </Col>
                    <Col span="10">
                        <Tag type="dot" :color="itemColor">赔率:</Tag>
                        <FormItem :prop="'items.' + index + '.rate'"
                                  :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                            <InputNumber :min="0" :step="0.01" v-model="item.rate"/>
                        </FormItem>
                    </Col>
                    <Col span="4" style="padding-top: 6px">
                        <Button type="dashed" size="small" icon="trash-a" @click="handleRemove(index)">删除</Button>
                    </Col>
                </Row>
                <Row>
                    <Col span="20">
                        <Button type="dashed" long @click="handleAdd" icon="plus-round" style="width: 366px">添加</Button>
                    </Col>
                    <Col span="4" style="padding-top: 5px">
                        <Button type="success" size="small" icon="android-refresh"
                                @click="formItem.items = deepCopy(jqsOdds)">恢复
                        </Button>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>

<script>
    export default {
        name: "jqs",
        props: {
            flag: String,
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
                itemColor: 'default',
                localItem: {items: []},
                formItem: {items: []},
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? '添加进球数赔率' : '编辑进球数赔率'
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
            jqsOdds () {
                let array = [];
                if (!this.item) return [];
                this.item.split(',').forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let score = isNaN(Number.parseFloat(arr[0])) ? null : Number.parseFloat(arr[0]);
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    array.push({score, rate});
                });
                this.formItem.items = this.deepCopy(array);
                return array;
            }
        },
        methods: {
            handleSubmit(name) {
                let items = this.formItem.items;
                if (items.length === 0) {
                    this.resetLoading();
                    this.$Message.warning('请添加参数后提交！');
                    return;
                }
                this.$refs[name].validate((valid) => {
                    this.resetLoading();
                    if (valid) {
                        let jqs = '';
                        items.forEach((item) => {
                            jqs += item.score + '|' + item.rate.toFixed(2) + ',';
                        });
                        jqs = jqs.replace(/,$/g, '');
                        this.$emit('updated', jqs);
                        this.modal = false;
                    }
                })
            },
            handleReset(name) {
                this.$refs[name].resetFields();
            },
            handleAdd() {
                let items = this.formItem.items;
                let index;
                if (items.length > 1) {
                    index = items[items.length - 1].score + 1
                } else if (items.length === 1) {
                    index = 2
                } else {
                    index = 1
                }
                items.push({score: index, rate: null});
            },
            handleRemove(index) {
                this.formItem.items.splice(index, 1);
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

