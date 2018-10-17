<template>
    <div>
        <div v-if="result">
            <Row>
                <Col span="3"><Tag color="primary">赛果</Tag></Col>
                <Col span="4">半全场： <span class="font-weight-bold">{{result.bqc}}</span></Col>
                <Col span="3">进球数： <span class="font-weight-bold">{{result.jqs}}</span></Col>
            </Row>
            <hr class="my-3" style="border:none;border-top:1px solid #e9eaec;">
        </div>
        <div>
            <spf :item="row.odds.nspf" :empty="invalid('nspf')" flag="nspf" class="mb-3"/>
            <spf :item="row.odds.spf" :empty="invalid('spf')" flag="spf" class="my-3" :rq="row.odds.rq"/>
            <jqs :item="row.odds.jqs" :empty="invalid('jqs')" class="my-3"/>
            <bqc :item="row.odds.bqc" :empty="invalid('bqc')" class="my-3"/>
            <bf :item="row.odds.bf" :empty="invalid('bf')" class="mt-3"/>
        </div>
    </div>
</template>
<script>
    import spf from './odds/spf';
    import jqs from './odds/jqs';
    import bf from './odds/bf';
    import bqc from './odds/bqc';
    export default {
        name: 'odds',
        components: {spf, jqs, bf, bqc},
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
                 if (!this.row.saiguo) return null;
                 let result = {};

                 let resultExp = new RegExp("\\((\\d:\\d)\\)\\s*(\\d:\\d)");
                 let array = resultExp.exec(this.row.saiguo);
                 let [homeTotalSore, awayTotalSore] = array[2].split(':');

                 result.jqs = +homeTotalSore + +awayTotalSore;
                 result.rqs = this.row.odds.rq;
                 result.bqc = this.row.saiguo;

                 return result
             }
        },
        methods: {
            invalid(str) {
                return this.row.odds[str] === undefined || this.row.odds[str] === null || this.row.odds[str].trim() === ''
            }
        }
    };
</script>
