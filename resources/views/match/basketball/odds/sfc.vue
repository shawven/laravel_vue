<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag :color="itemColor">{{tagText}}</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Tag v-for="(item, index) in sfcOdds" :key="index" type="border" :color="itemColor">
                    {{item.range.start === 26
                        ? (item.range.start + ' +')
                        : (item.range.start + '-' + item.range.end)
                    }}分&nbsp;&nbsp;{{item.rate}}
                </Tag>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>
        <Modal v-if="editor" v-model="modal" :loading="loading" :title="title" @on-ok="handleSubmit('formItem')"
               class="top-modal" width="600">
            <Form ref="formItem" :model="formItem" inline>
                <Row v-for="(item, index) in formItem.items" :key="index">
                    <Col span="11">
                        <Tag type="dot" :color="itemColor">倍数:</Tag>
                        <FormItem :prop="'items.' + index + '.multiple'"
                                  :rules="{required: true, type: 'number', message: '请填写倍数', trigger: 'blur'}">
                            <InputNumber :max="6" :min="1" :step="1" v-model="item.multiple"/>
                        </FormItem>
                        {{((item.multiple - 1) * 5 + 1) === 26
                            ? (((item.multiple - 1) * 5 + 1) + '+')
                            : (((item.multiple - 1) * 5 + 1) + '-' + (item.multiple * 5))
                        }}分
                    </Col>
                    <Col span="9">
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
                                @click="formItem.items = deepCopy(sfcOdds)">恢复
                        </Button>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>

<script>
    export default {
        name: "sfc",
        props: {
            flag: String,
            itemColor: String,
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
                formItem: {items: []},
                isAway: this.flag === 'sfc_k',
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item
                    ? (this.isAway ? "添加客场胜分差赔率" : '添加主场胜分差赔率')
                    : (this.isAway ? "修改客场胜分差赔率" : '修改主场胜分差赔率')
            },
            tagText() {
                return this.isAway ? "胜分差（客）" : '胜分差（主）';
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
            sfcOdds () {
                let array = [];
                this.formItem.items = [];
                if (!this.item) return [];
                this.item.split(',').forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let multiple = isNaN(Number.parseFloat(arr[0].charAt(1))) ? null : Number.parseFloat(arr[0].charAt(1));
                    let range = {start: (multiple - 1) * 5 + 1, end:  multiple * 5};
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    array.push({range, multiple, rate});
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
                        let sfc = '';
                        items.forEach((item) => {
                            sfc += (this.isAway ? ('1' + item.multiple) : ('0' + item.multiple))
                                + '|' + item.rate.toFixed(2) + ',';
                        });
                        sfc = sfc.replace(/,$/, '');
                        this.$emit('updated', sfc);
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
                    index = items[items.length - 1].multiple + 1
                } else if (items.length === 1) {
                    index = 2
                } else {
                    index = 1
                }

                if (index > 6) {
                    this.$Message.warning('目前只能添加26+分');
                    return
                }
                items.push({multiple: index, rate: null});
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

