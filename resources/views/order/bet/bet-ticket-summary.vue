<template>
    <Row>
        <template v-for="combination in item">
            <Col span="24" class="border rounded p-2 my-1" :class="combinationClass(combination)">
                <Row type="flex" justify="center" align="middle">
                    <Col span="22">
                        <Row v-for="(descItem, index) in combination" :key="index" class="text-center" :gutter="16">
                            <Col span="8">
                                <Tag color="error"><span>{{descItem.item.homesxname}}</span></Tag>
                                <Icon type="arrow-swap" class="mx-2"/>
                                <Tag color="success"><span>{{descItem.item.awaysxname}}</span></Tag>
                            </Col>
                            <Col span="4">
                                {{matchResult(descItem.type, descItem.item)}}
                            </Col>
                            <Col span="3">
                                {{playTags[descItem.type][descItem.play]}}
                            </Col>
                            <Col span="6" :class="{'text-green': descItem.win}">
                                {{victoryName(descItem.type, descItem.item, descItem.play,  descItem.guess)}}
                            </Col>
                            <Col span="3">
                                {{descItem.odds}}
                            </Col>
                        </Row>
                    </Col>
                    <Col span="2" class="text-center">
                        <span v-if="combinationResultIsWin(combination)">{{getCombinationOdds(combination)}}</span>
                        <span v-else-if="combinationResultIsNull(combination)"  class="text-gray-700"><Icon type="load-a"></Icon></span>
                        <span v-else class="text-red"><Icon type="close"></Icon></span>
                    </Col>
                </Row>
            </Col>
        </template>
    </Row>
</template>

<script>
    import calculation from '@/views/order/bet/calculation'

    export default {
        name: "bet-ticket-summary",
        props: {
            item: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        data() {
            return {
                playTags:{
                    jclq: {
                        sf: '胜负',
                        rfsf: '让分胜负',
                        dxf: '大小分',
                        sfc_k: '胜分差（客）',
                        sfc_z: '胜分差（主）',
                    },
                    jczq: {
                        rq: '让球数',
                        nspf: '胜平负',
                        spf: '让分胜平负',
                        bf: '比分',
                        jqs: '进球数',
                        bqc: '半全场'
                    }
                },
                result: {
                    jclq: {2: '客场', 1: '主场'},
                    jczq: {3: '主场', 1: '平局', 0: '客场'},
                }
            }
        },
        methods: {
            combinationClass(combination) {
                return this.combinationResultIsWin(combination)
                    ? 'border-green'
                    : (this.combinationResultIsNull(combination) ? 'border-gary-700' : 'border-red')
            },
            combinationResultIsWin(combination) {
                return calculation.combinationResultIsWin(combination)
            },
            combinationResultIsNull(combination) {
                return calculation.combinationResultIsNull(combination)
            },
            getCombinationOdds(combination) {
                return calculation.getCombinationOdds(combination)
            },
            matchResult(type, match) {
                let result = '';
                switch (type) {
                    case 'jclq':
                        result = '';
                        break;
                    case 'jczq':
                        result = match.saiguo;
                        break;
                    default:
                        result = '';
                        break;
                }
                return !!result ? result : '-';
            },
            victoryName(type, match, play, guess) {
                switch (type) {
                    case 'jclq':
                        return +guess === 2 ? match.awaysxname : match.homesxname;
                    case 'jczq':
                        return this.footballVictoryName(match, play, guess);
                    default:
                        return {}
                }
            },
            footballVictoryName(match, play, guess) {
                let str = '';
                switch (play) {
                    case 'bf' :
                        let diffScore = guess.length === 2
                            ? +guess.charAt(0) === +guess.charAt(1)
                            : +guess.charAt(0) === +guess.charAt(2);
                        str = diffScore > 0
                            ? match.homesxname
                            : (+guess < 0 ? match.awaysxname : '平局');
                        str += '（'+ ( guess.length === 2
                            ? guess.charAt(0) + ':' + guess.charAt(1)
                            : guess ) +'）';
                        break;
                    default:
                        str = +guess === 0
                            ? match.awaysxname
                            : (+guess === 3 ? match.homesxname : '平局')
                }

                return str;
            }
        }
    }
</script>
