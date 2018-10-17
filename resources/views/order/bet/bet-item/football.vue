<template>
    <div>
        <Row v-for="(match, index1) in item.items" :key="index1"
             type="flex" justify="center" align="middle" :gutter="16">
            <Col span="24" v-if="index1 !== 0">
                <hr class="my-2" style="border:none;border-top:1px solid #e9eaec;">
            </Col>
            <Col span="8">
                <Tag color="error"><span>{{match.homesxname}}</span></Tag>
                <Icon type="arrow-swap" class="mx-2"/>
                <Tag color="success"><span>{{match.awaysxname}}</span></Tag>
            </Col>
            <Col span="4">
                {{match.saiguo ? match.saiguo : '-'}}
            </Col>
            <Col span="12">
                <Row v-for="(betDetail, index2) in match.betDetails" :key="index2" :gutter="16"
                     type="flex" justify="center" align="middle">
                    <Col span="6">
                        {{playTags[betDetail.play]}}
                    </Col>
                    <Col span="12">
                        {{victoryName(match, betDetail.play, betDetail.guess)}}
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
        name: "football",
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
                result: {3: '主场', 1: '平局', 0: '客场'},
                playTags: {
                    rq: '让球数',
                    nspf: '胜平负',
                    spf: '让分胜平负',
                    bf: '比分',
                    jqs: '进球数',
                    bqc: '半全场'
                },
                bqc: {
                    '00': '负负',
                    '01': '负平',
                    '03': '负胜',
                    '10': '平负',
                    '11': '平平',
                    '13': '平胜',
                    '30': '胜负',
                    '31': '胜平',
                    '33': '胜胜'
                },
                bf: {
                    '3A' : '胜其他',
                    '1A' : '平其他',
                    '0A' : '负其他',
                }
            }
        },
        methods: {
            victoryName(match, play, guess) {
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

