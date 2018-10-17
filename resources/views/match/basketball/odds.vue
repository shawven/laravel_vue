<template>
    <div>
        <div v-show="row.result">
            <Row type="flex" justify="center" align="middle">
                <Col span="3">
                    <Tag type="dot" color="primary">{{result.Competition_team}}</Tag>
                </Col>
                <Col span="21">
                    <Row>
                        <Col span="4"><Tag color="IndianRed">总比分</Tag> {{result.Final_score}}</Col>
                        <Col span="4"><Tag color="Coral">第一节</Tag> {{result.Section_one}}</Col>
                        <Col span="4"><Tag color="Coral">第二节</Tag> {{result.Section_two}}</Col>
                        <Col span="4"><Tag color="Coral">第三节</Tag> {{result.Section_three}}</Col>
                        <Col span="4"><Tag color="Coral">第四节</Tag> {{result.Section_four}}</Col>
                        <Col span="4" v-if="result.Time_score"><Tag color="Coral">加时赛</Tag> {{result.Time_score}}</Col>
                    </Row>
                </Col>
            </Row>
            <hr class="my-3" style="border:none;border-top:1px solid #e9eaec;">
        </div>
        <div>
            <sf :item="row.odds.sf" />
            <rfsf :item="row.odds.rfsf" />
            <dxf :item="row.odds.dxf" />
            <sfc :item="row.odds.sfc_z" flag='sfc_z' item-color="error"/>
            <sfc :item="row.odds.sfc_k" flag='sfc_k' item-color="success"/>
        </div>
    </div>
</template>
<script>
    import sf from './odds/sf';
    import dxf from './odds/dxf';
    import rfsf from './odds/rfsf';
    import sfc from './odds/sfc';
    export default {
        name: 'odds',
        components: {sf, dxf, rfsf, sfc},
        props: {
            row: {
                type: Object,
                default() {
                    return {};
                }
            },
        },
        computed: {
            result() {
                if (!this.row.result) return {};

                let result = {...this.row.result};
                result.Competition_team = result.Competition_team.split('VS').reverse().join(' VS ');
                result.Final_score = result.Final_score.split(':').reverse().join(':');
                result.Section_one = result.Section_one.split(':').reverse().join(':');
                result.Section_two = result.Section_two.split(':').reverse().join(':');
                result.Section_three = result.Section_three.split(':').reverse().join(':');
                result.Section_four = result.Section_four.split(':').reverse().join(':');
                result.Time_score = result.Time_score.split(':').reverse().join(':');

                return result;
            }
        }
    };
</script>
