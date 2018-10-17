<template>
    <div class="login" @keydown.enter="handleSubmit">
        <Row type="flex" justify="space-around" align="middle" class="h-100">
            <Col span="24" class="title">
                <Row type="flex" justify="center" align="middle" class="h-100">
                    <h1>疯狂彩票管理后台</h1>
                </Row>
            </Col>
            <Col span="24" class="login-form">
                <Card>
                    <p slot="title" class="text-center">
                        <Icon type="md-log-in" :size="16"/>
                        欢迎登录
                    </p>
                    <div class="form-con">
                        <Form ref="loginForm" :model="form" :rules="rules">
                            <FormItem prop="username">
                                <Input v-model="form.username" placeholder="请输入用户名">
                                <span slot="prepend"><Icon :size="16" type="ios-contact"></Icon></span>
                                </Input>
                            </FormItem>
                            <FormItem prop="password">
                                <Input type="password" v-model="form.password" placeholder="请输入密码">
                                <span slot="prepend"><Icon :size="14" type="ios-lock"></Icon></span>
                                </Input>
                            </FormItem>
                            <FormItem>
                                <Button @click="handleSubmit" type="primary" long :loading="loading">登录</Button>
                            </FormItem>
                        </Form>
                    </div>
                </Card>
            </Col>
            <Col span="24" class="footer">
                <Row :gutter="32" type="flex" justify="center" align="middle" class="h-100">
                    <Col>帮助</Col>
                    <Col>隐私</Col>
                    <Col>条款</Col>
                    <Col><span>Copyright © 2018</span></Col>
                </Row>
            </Col>
        </Row>
    </div>
</template>

<script>
    import {pageRoutes} from '@/router/router';

    export default {
        name: 'login',
        data() {
            return {
                vedioCanPlay: false,
                fixStyle: '',
                loading: false,
                form: {
                    username: '',
                    password: ''
                },
                rules: {
                    username: [
                        {required: true, message: '用户名不能为空', trigger: 'blur'}
                    ],
                    password: [
                        {required: true, message: '密码不能为空', trigger: 'blur'}
                    ]
                }
            };
        },
        mounted: function () {
            this.onresize()
        },
        methods: {
            handleSubmit() {
                this.$refs.loginForm.validate((valid) => {
                    this.loading = true;
                    if (valid) {
                        this.login();
                    } else {
                        this.loading = false;
                    }
                });
            },
            login() {
                this.$http.get('/api/attempt')
                    .then(() => {
                        this.$messageHttp.post('api/login', this.form, (result) => this.loginSuccess(result), null,
                            () => this.loading = false
                        )
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$http.handler.handleError(error);
                    })

            },
            loginSuccess(result) {
                result.data.avatar = result.data.avatar
                    || 'https://o5wwk8baw.qnssl.com/a42bdcc1178e62b4694c830f028db5c0/avatar';

                sessionStorage.setItem('userInfo', JSON.stringify(result.data));

                this.$store.commit('setUserInfo', result.data);
                this.$router.push({name: 'home_index'});

                this.$store.dispatch('getRoutes')
                    .then(() => {
                        this.$router.addRoutes([...this.$store.getters.routes, ...pageRoutes]);
                    })
                    .catch((error) => {
                        this.$http.handler.handleError(error)
                    })
            },
            onresize() {
                window.onresize = () => {
                    const windowWidth = document.body.clientWidth;
                    const windowHeight = document.body.clientHeight;
                    const windowAspectRatio = windowHeight / windowWidth;
                    let videoWidth;
                    let videoHeight;
                    if (windowAspectRatio < 0.5625) {
                        videoWidth = windowWidth;
                        videoHeight = videoWidth * 0.5625;
                        this.fixStyle = {
                            height: windowWidth * 0.5625 + 'px',
                            width: windowWidth + 'px',
                            'margin-bottom': (windowHeight - videoHeight) / 2 + 'px',
                            'margin-left': 'initial'
                        }
                    } else {
                        videoHeight = windowHeight;
                        videoWidth = videoHeight / 0.5625;
                        this.fixStyle = {
                            height: windowHeight + 'px',
                            width: windowHeight / 0.5625 + 'px',
                            'margin-left': (windowWidth - videoWidth) / 2 + 'px',
                            'margin-bottom': 'initial'
                        }
                    }
                };
                window.onresize();
            }
        }
    };
</script>

<style lang="less">
    .login {
        height: 100vh;
        background: #fff url('/storage/bg.svg');
        .title {
            text-align: center;
            vertical-align: center;
            color: #2d8cf0;
            font-size: 1rem;
            z-index: 3;
        }
        .footer {
            color: #2d8cf0;
            font-size: 1rem;
            z-index: 3;
        }
        &-form {
            margin-top: 2rem;
            width: 300px;
            z-index: 3;
            &-header {
                font-size: 16px;
                font-weight: 300;
                text-align: center;
                padding: 30px 0;
            }
            .form-con {
                padding: 10px 0 0;
            }
            .login-tip {
                font-size: 10px;
                text-align: center;
                color: #c3c3c3;
            }
        }
    }
</style>
