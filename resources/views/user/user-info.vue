<template>
    <Poptip width="400" title="用户信息" class="user-info"
            @on-popper-show="visible = true" @on-popper-hide="visible = false"  placement="right">
        <Avatar v-if="item.avatar" :src="item.avatar" ></Avatar>
        <Avatar v-else class="text-center">{{item.usernick.charAt(0)}}</Avatar>
        <a>{{item.usernick}}</a>
        <Card :bordered="false" dis-hover class="user-info-card"  slot="content">
            <div slot="title">
                <Avatar v-if="item.avatar" :src="item.avatar" ></Avatar>
                <Avatar v-else class="text-center">{{item.usernick.charAt(0)}}</Avatar>
                <span class="font-weight-bold text-dark">{{item.usernick}}</span>
                <span class="font-weight-bold text-dark ml-4">UID：{{item.id}}</span>
            </div>
            <Row>
                <Col span="8">
                    <Tag color="primary">用户名</Tag>
                </Col>
                <Col span="16" >
                    <Icon type="person"></Icon><span class="font-weight-bold text-dark"> {{item.userName}}</span>
                </Col>
            </Row>
            <Row v-if="item.real_name">
                <Col span="8">
                    <Tag color="primary">真实姓名</Tag>
                </Col>
                <Col span="16">
                    <span class="font-weight-bold text-dark">{{item.real_name}}</span>
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">手机号码</Tag>
                </Col>
                <Col span="16" >
                    <Icon type="iphone"></Icon><span class="font-weight-bold text-dark"> {{item.userPhone}}</span>
                </Col>
            </Row>
            <Row v-if="item.real_phone">
                <Col span="8">
                    <Tag color="primary">真实号码</Tag>
                </Col>
                <Col span="16" >
                    <Icon type="iphone"></Icon><span class="font-weight-bold text-dark"> {{item.real_phone}}</span>
                </Col>
            </Row>
            <Row v-if="item.real_card">
                <Col span="8">
                    <Tag color="primary">身份证</Tag>
                </Col>
                <Col span="16" >
                    <Icon type="card"></Icon><span class="font-weight-bold text-dark"> {{item.real_phone}}</span>
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">实名认证</Tag>
                </Col>
                <Col span="16" class="font-weight-bold text-dark">
                    <Tag v-if="item.isRealAttestation !== undefined && item.isRealAttestation !== null"
                         :color="stateColor[item.isRealAttestation]">{{stateText[item.isRealAttestation]}}
                    </Tag>
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">钱包</Tag>
                </Col>
                <Col span="16">
                    <span class="font-weight-bold text-dark">{{item.wallet}}</span> ￥
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">余额</Tag>
                </Col>
                <Col span="16">
                    <span class="font-weight-bold text-dark">{{item.balance}}</span> ￥
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">可提现</Tag>
                </Col>
                <Col span="16">
                    <span class="font-weight-bold text-dark">{{item.draw_balance}}</span> ￥
                </Col>
            </Row>
            <Row>
                <Col span="8">
                    <Tag color="primary">彩金</Tag>
                </Col>
                <Col span="16">
                    <span class="font-weight-bold text-dark">{{item.handsel}}</span> ￥
                </Col>
            </Row>
        </Card>
    </Poptip>
</template>

<script>
    export default {
        name: 'user-info',
        props: {
            title: String,
            item: {
                type: Object,
                default: {},
            },
        },
        data() {
            return {
                stateText: ['', '未认证', '已认证'],
                stateColor: ['', 'default', 'success'],
                visible: false,
            }
        }
    };
</script>

<style lang="less">
    .order-item-card {
        .ivu-card-body .ivu-row {
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }
    }
    .user-info .user-info-card .ivu-avatar-string{
        transform: scale(1);
        position: absolute;
        display: inline-block;
        left: calc(~"50% - 7px") !important;
    }
</style>
