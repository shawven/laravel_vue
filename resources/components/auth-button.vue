<template>
    <Button :type="buttonInfo.type" :icon="buttonInfo.icon" :size="buttonInfo.size ? buttonInfo.size : size"
            :disabled="buttonInfo.disabled ? buttonInfo.disabled : disabled" @click="$emit('click')">
        {{buttonInfo.title}}
    </Button>
</template>

<script>
    export default {
        name: "auth-button",
        props: {
            size: String,
            disabled: Boolean,
            name: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                buttons: this.getButtons(this.$route.meta)
            }
        },
        computed: {
            buttonInfo() {
                let forbidden = {
                    type: 'dashed',
                    title: `无权限（${this.name}）`,
                    size: 'default',
                    icon: 'ios-close-outline',
                    disabled: true
                };
                if (!this.buttons.length) return this.$store.getters.isSuperAdmin ? forbidden : forbidden;
                let find = this.buttons.find((button) => button.name === this.name);
                return !!find ? find : this.$store.getters.isSuperAdmin ? forbidden : forbidden
            }
        },
        methods: {
            getButtons(items = []) {
                if (!items instanceof Array || '{}' === JSON.stringify(items)) return [];

                let buttons = [];

                if (items.length === 0) {
                    throw new Error('没有配置按钮权限 ' + name);
                } else{
                    items.forEach((item) => {
                        buttons.push(item);
                        if (item.meta) {
                            buttons.push(...this.getButtons(item.meta))
                        }
                    });
                    return buttons;
                }
            }
        }
    }
</script>

<style scoped>

</style>
