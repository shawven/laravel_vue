<template>
    <Row style="height:47px">
        <Col class="d-inline-block text-center w-100">
            <div class="position-absolute" style="margin-left: 20px">
                <Icon type="android-list"></Icon>
            </div>
            <div>{{title}}</div>
        </Col>
    </Row>
</template>

<script>
    export default {
        name: 'Header',
        props: {
           title: String
        },
        mounted() {
            this.display()
        },
        methods: {
            display() {
                let style =  Boolean(this.title) ? 'block' : 'none';
                document.querySelector('.list-table .ivu-table-title').style.display = style;
            }
        }
    };
</script>
