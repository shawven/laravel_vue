<template>
    <div>
        <Row type="flex" justify="center" align="middle">
            <Col span="3">
                <Tag color="primary">比分</Tag>
            </Col>
            <Col :span="editor ? 19 : 21">
                <Row>
                    <Tag :color="awayColor">主</Tag>
                    <Tag v-for="(item, index) in bfOdds.home.items" :key="index" type="border" :color="awayColor">
                        {{item.score.home}}:{{item.score.away}}&nbsp;&nbsp;{{item.rate}}
                    </Tag>
                    <Tag v-if="bfOdds.home.other.rate"  type="border" :color="awayColor">
                        胜其他 &nbsp;&nbsp;{{bfOdds.home.other.rate}}
                    </Tag>
                </Row>

                <Row>
                    <Tag :color="drawColor">平</Tag>
                    <Tag v-for="(item, index) in bfOdds.draw.items" :key="index" type="border" :color="drawColor">
                        {{item.score.home}}:{{item.score.away}}&nbsp;&nbsp;{{item.rate}}
                    </Tag>
                    <Tag v-if="bfOdds.draw.other.rate"  type="border" :color="drawColor">
                        平其他 &nbsp;&nbsp;{{bfOdds.draw.other.rate}}
                    </Tag>
                </Row>

                <Row>
                    <Tag :color="homeColor">客</Tag>
                    <Tag v-for="(item, index) in bfOdds.away.items" :key="index" type="border" :color="homeColor">
                        {{item.score.home}}:{{item.score.away}}&nbsp;&nbsp;{{item.rate}}
                    </Tag>
                    <Tag v-if="bfOdds.away.other.rate"  type="border" :color="homeColor">
                        负其他 &nbsp;&nbsp;{{bfOdds.away.other.rate}}
                    </Tag>
                </Row>
            </Col>
            <Col span="2" v-if="editor">
                <Button :type="buttonType" @click="modal = true" size="small"
                        :icon="buttonIcon" class="ml-1">{{buttonText}}</Button>
            </Col>
        </Row>

        <Modal v-if="editor" v-model="modal" :loading="loading" :title="title" @on-ok="handleSubmit('formItem')"
               class="top-modal"  width="1150">
            <Form ref="formItem" :model="formItem" :label-width="60">
                <Row class="football-bf-editor" :gutter="8">
                    <Col span="8">
                        <Row class="bg-danger text-white text-center font-lg mb-2"><Col><p>主</p></Col></Row>
                        <Row v-for="(item, index) in formItem.home.items" :key="index">
                            <Col span="6">
                                <FormItem label="主场" :prop="'home.items.' + index + '.score.home'"
                                          :rules="{required: true, type: 'number', message: '请填写主场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.home" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="客场" :prop="'home.items.' + index + '.score.away'"
                                          :rules="{required: true, type: 'number', message: '请填写客场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.away" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="赔率" :prop="'home.items.' + index + '.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="item.rate" style="width: 55px"/>
                                </FormItem>
                            </Col>
                            <Col span="4" offset="2" class="delete">
                                <Button type="dashed" size="small" icon="trash-a"
                                        @click="handleRemove(index, 'home')">删除</Button>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="6">
                                <FormItem label="胜其他" :prop="'home.other.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="formItem.home.other.rate"/>
                                </FormItem>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="18">
                                <Button type="dashed" long @click="handleAdd('home')" icon="plus-round" class="add">添加</Button>
                            </Col>
                            <Col span="4" offset="2" class="refresh">
                                <Button type="success" size="small" icon="android-refresh"
                                        @click="formItem.home = deepCopy(bfOdds.home)">恢复
                                </Button>
                            </Col>
                        </Row>
                    </Col>

                    <Col span="8">
                        <Row class="bg-gray-500 text-white text-center font-lg mb-2"><Col><p>平</p></Col></Row>
                        <Row v-for="(item, index) in formItem.draw.items" :key="index">
                            <Col span="6">
                                <FormItem label="主场" :prop="'draw.items.' + index + '.score.home'"
                                          :rules="{required: true, type: 'number', message: '请填写主场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.home" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="客场" :prop="'draw.items.' + index + '.score.away'"
                                          :rules="{required: true, type: 'number', message: '请填写客场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.away" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="赔率" :prop="'draw.items.' + index + '.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="item.rate" style="width: 55px"/>
                                </FormItem>
                            </Col>
                            <Col span="4" offset="2" class="delete">
                                <Button type="dashed" size="small" icon="trash-a"
                                        @click="handleRemove(index, 'draw')">删除</Button>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="9">
                                <FormItem label="平其他" :prop="'draw.other.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="formItem.draw.other.rate"/>
                                </FormItem>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="18">
                                <Button type="dashed" long @click="handleAdd('draw')" icon="plus-round" class="add">添加</Button>
                            </Col>
                            <Col span="4" offset="2" class="refresh">
                                <Button type="success" size="small" icon="android-refresh"
                                        @click="formItem.draw = deepCopy(bfOdds.draw)">恢复
                                </Button>
                            </Col>
                        </Row>
                    </Col>

                    <Col span="8">
                        <Row class="bg-success text-center font-lg mb-2 text-white"><Col><p>客</p></Col></Row>
                        <Row v-for="(item, index) in formItem.away.items" :key="index">
                            <Col span="6">
                                <FormItem label="主场" :prop="'away.items.' + index + '.score.home'"
                                          :rules="{required: true, type: 'number', message: '请填写主场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.home" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="客场" :prop="'away.items.' + index + '.score.away'"
                                          :rules="{required: true, type: 'number', message: '请填写客场分数', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="1" v-model="item.score.away" style="width: 40px"/>
                                </FormItem>
                            </Col>
                            <Col span="6">
                                <FormItem label="赔率" :prop="'away.items.' + index + '.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="item.rate" style="width: 55px"/>
                                </FormItem>
                            </Col>
                            <Col span="4" offset="2" class="delete">
                                <Button type="dashed" size="small" icon="trash-a"
                                        @click="handleRemove(index, 'away')">删除</Button>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="9">
                                <FormItem label="负其他" :prop="'away.other.rate'"
                                          :rules="{required: true, type: 'number', message: '请填写赔率', trigger: 'blur'}">
                                    <InputNumber :min="0" :step="0.01" v-model="formItem.away.other.rate"/>
                                </FormItem>
                            </Col>
                        </Row>
                        <Row>
                            <Col span="18">
                                <Button type="dashed" long @click="handleAdd('away')" icon="plus-round" class="add">添加</Button>
                            </Col>
                            <Col span="4" offset="2" class="refresh">
                                <Button type="success" size="small" icon="android-refresh"
                                        @click="formItem.away = deepCopy(bfOdds.away)">恢复
                                </Button>
                            </Col>
                        </Row>
                    </Col>
                </Row>
            </Form>
        </Modal>
    </div>
</template>

<script>
    export default {
        name: "bf",
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
                    home: {items: [], other: {rate: null}},
                    draw: {items: [], other: {rate: null}},
                    away: {items: [], other: {rate: null}}
                },
                formItem: {},
                homeColor: 'success',
                drawColor: 'gray',
                awayColor: 'error',
                loading: true,
                modal: false
            }
        },
        computed: {
            title() {
                return !this.item ? "添加比分赔率": '修改比分赔率'
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
            bfOdds () {
                let odds = this.deepCopy(this.localItem);
                this.formItem = this.deepCopy(odds);
                if (!this.item) return odds;
                let item = this.item;
                const home = '3A';
                const draw = '1A';
                const away = '0A';

                let homeStr = '';
                let drawStr = '';
                let awayStr = '';
                let hasHome = item.includes(home);
                let hasDraw = item.includes(draw);
                let hasAway = item.includes(away);

                if (hasHome && hasDraw) {
                    homeStr = item.slice(0, item.indexOf(draw)).replace(/,$/, '')
                }
                if (hasHome && hasDraw && hasAway) {
                    drawStr = item.slice(item.indexOf(draw), item.indexOf(away)).replace(/,$/, '')
                }
                if (drawStr && hasAway) {
                    awayStr = item.slice(item.indexOf(away)).replace(/,$/, '')
                }

                odds = {
                    home: this.explodeOdds(homeStr, home),
                    draw: this.explodeOdds(drawStr, draw),
                    away: this.explodeOdds(awayStr, away)
                };
                this.formItem = this.deepCopy(odds);
                return odds;
            }
        },
        methods: {
            explodeOdds(str, tag) {
                let other = {};
                let items = [];
                if (str === '') return {items, other};
                str.split(',').forEach((item) => {
                    if (!item) return;
                    let arr = item.split('|', 2);
                    let rate = isNaN(Number.parseFloat(arr[1])) ? null : Number.parseFloat(arr[1]);
                    if (tag === arr[0]) {
                        other = {tag, rate};
                        return
                    }
                    let score = {
                        home: isNaN(Number.parseFloat(arr[0].charAt(0))) ? null : Number.parseFloat(arr[0].charAt(0)),
                        away: isNaN(Number.parseFloat(arr[0].charAt(1))) ? null : Number.parseFloat(arr[0].charAt(1))
                    };
                    items.push({score, rate});
                });
                return {items, other};
            },
            implodeOdds(obj) {
                let otherStr = obj.other.tag + '|' + obj.other.rate + ',';
                let itemsStr = '';
                obj.items.forEach((item) => {
                    itemsStr += ('' + item.score.away) + ('' + item.score.home) + '|' + item.rate.toFixed(2) + ',';
                });
                return otherStr + itemsStr.replace(/,$/, '');
            },
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    this.resetLoading();
                    if (valid) {
                        let bf = this.implodeOdds(this.formItem.draw) + ',' + this.implodeOdds(this.formItem.victory) + ','
                            + this.implodeOdds(this.formItem.defeat);
                        this.$emit('updated', bf);
                        this.modal = false;
                    }
                })
            },
            handleAdd(name) {
                let items = this.formItem[name].items;
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
            handleRemove(index, name) {
                this.formItem[name].items.splice(index, 1);
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

<style lang="less">
    .football-bf-editor {
        max-height: 750px;
        overflow-y: auto;
        .ivu-input-number {
            width: 70px;
        }
        .add {
            width: 295px;
        }
        .delete {
            padding-top: 6px
        }
        .refresh {
            padding-top: 5px
        }
    }
</style>
