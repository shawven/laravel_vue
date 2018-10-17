let calculation = {
    getTotalOdds(combinations) {
        let totalOdds = 0;
        for(let combination of combinations) {
            totalOdds += this.combinationResultIsWin(combination)
                ? +this.getCombinationOdds(combination)
                : 0;
        }
        return this.roundOdds(totalOdds);
    },
    convert(obj) {
        let array = [];
        for(let key in obj) {
            array.push(...obj[key])
        }
        return array
    },
    getCombinationOdds(combination) {
        if (!this.combinationResultIsWin(combination)) {
            return ''
        }

        let odds = 1;
        for (let key in combination) {
            odds *= +combination[key].odds
        }
        return this.roundOdds(odds);
    },
    combinationResultIsWin(combination) {
        for (let key in combination) {
            if (combination[key].win !== true) {
                return false;
            }
        }
        return true
    },
    combinationResultIsNull(combination) {
        for (let key in combination) {
            if (combination[key].win !== null) {
                return false;
            }
        }
        return true
    },
    // 赔率计算，保留三位小数（小数点第三位的值为 0|5， 第三位四舍五入0|5）
    roundOdds(num) {
        let [integer, decimal] = num.toFixed(3).split('.');

        if (decimal) {
            let lastPosition = decimal.slice(-1);
            let newLastPosition = lastPosition > 0
                ? (lastPosition >= 5 ? 5 : 0)
                : 0;
            decimal = decimal.slice(0, -1) + newLastPosition;
        }

        let odds = Number.parseFloat(integer + '.' + decimal);
        return odds ? odds.toFixed(3) : 0;
    }
};

export default calculation;
