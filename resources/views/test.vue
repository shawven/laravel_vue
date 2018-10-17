<style>

</style>
<template>
    <div style=" background-color: white !important">
        <Form ref="formItem" :model="formItem"  inline :label-width="80">
            <FormItem  label="范围">
                <DatePicker type="datetimerange" split-panels v-model="formItem.date" style="width: 300px"></DatePicker>
            </FormItem>
            <FormItem  label="开始">
                <DatePicker type="datetime" style="width: 200px" v-model="formItem.datetime[0]"></DatePicker>
            </FormItem>
            <FormItem  label="结束">
                <DatePicker type="datetime"  style="width: 200px" v-model="formItem.datetime[1]"></DatePicker>
            </FormItem>
            <FormItem>
                <Button type="primary" @click="handleSubmit('formItem')">Signin</Button>
            </FormItem>
        </Form>
    </div>
</template>
<script>
    export default {
        data () {
            return {
                formItem: {
                    date: null,
                    date1: null,
                    datetime: [],
                    user: '',
                    password: ''
                }
            }
        },
        methods: {
            handleSubmit(name) {
                console.log(this.formItem.datetime[0] )
                console.log(new Date() )
                console.log(new Date((new Date().getTime())))

            }
        }
    }
</script>
