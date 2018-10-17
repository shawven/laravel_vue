<template>
    <div>
        div
        <Row v-for="(match, index1) in item.items" :key="index1"
             type="flex" justify="center" align="middle" :gutter="16">
            <Col span="24">
                <hr class="my-1" style="border:none;border-top:1px solid #e9eaec;">
            </Col>
            <Col span="8">
                <Tag color="error"><span>{{match.homesxname}}</span></Tag>
                <Icon type="arrow-swap" class="mx-2"/>
                <Tag color="success"><span>{{match.awaysxname}}</span></Tag>
            </Col>
            <Col span="4">
                <!--{{match.saiguo}}--> -
            </Col>
            <Col span="12">
                <Row v-for="(betDetail, index2) in match.betDetails" :key="index2" :gutter="16"
                     type="flex" justify="center" align="middle" >
                    <Col span="8">
                        {{playTags[betDetail.play]}}
                    </Col>
                    <Col span="6">
                        {{victoryName(match, betDetail.guess)}}
                    </Col>
                    <Col span="6">
                        {{betDetail.odds}}
                    </Col>
                </Row>
            </Col>
        </Row>
    </div>
</template>

<script>
    export default {
        name: "basketball",
        props: {
            item: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                result: {2: '客场', 1: '主场'},
                playTags:{
                    sf: '胜负',
                    rfsf: '让分胜负',
                    dxf: '大小分',
                    sfc_k: '胜分差（客）',
                    sfc_z: '胜分差（主）',
                }
            }
        },
        methods: {
            victoryName(match, guess) {
                return +guess === 2
                    ? match.awaysxname
                    :  match.homesxname
            }
        }
    }
</script>

<style scoped>

</style>
