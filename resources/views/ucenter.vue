<template>
    <div>
        <Card>
            <p slot="title"><Icon type="person"></Icon>个人信息
            </p>
            <div>
                <Form ref="userForm" :model="userForm" :label-width="100" label-position="right" :rules="inforValidate">
                    <FormItem label="用户名：" prop="username">
                        <span>{{userForm.username}}</span>
                    </FormItem>
                    <FormItem label="用户头像：">
                        <div style="width:300px;">
                            <Avatar :src="userForm.avatar" size="large" style="background: #619fe7;margin-right: 10px; float: left"/>
                            <Upload ref="upload" :action="uploadUrl" :headers="uploadHeaders" name="avatar"
                                    :on-success="uploadSuccess">
                                <Avatar size="large">
                                    <Icon type="camera" size="36" style="width:40px;height:40px;line-height:40px;cursor:pointer;"></Icon>
                                </Avatar>
                            </Upload>
                        </div>
                    </FormItem>
                    <FormItem label="用户昵称：" prop="nickname">
                        <div style="width:300px;">
                            <Input v-model="userForm.nickname"></Input>
                        </div>
                    </FormItem>
                    <FormItem label="用户邮箱：">
                        <div style="width:300px;">
                            <Input v-model="userForm.email"></Input>
                        </div>
                    </FormItem>
                    <FormItem label="真实姓名：" prop="real_name">
                        <div style="width:300px;">
                            <Input v-model="userForm.real_name"></Input>
                        </div>
                    </FormItem>
                    <FormItem label="用户手机：" prop="mobile">
                        <div style="width:204px;">
                            <Input v-model="userForm.mobile" />
                        </div>
                    </FormItem>
                    <FormItem label="所属部门：">
                        <div style="width:300px;">
                            <Input v-model="userForm.department"></Input>
                        </div>
                    </FormItem>
                    <FormItem label="登录密码：">
                        <Button type="text" size="small" icon="md-create" @click="showEditPassword">修改密码</Button>
                    </FormItem>
                    <div>
                        <Button type="text" style="width: 100px;" @click="cancelEditUserInfor">取消</Button>
                        <Button type="primary" style="width: 100px;" :loading="save_loading" @click="saveEdit">保存
                        </Button>
                    </div>
                </Form>
            </div>
        </Card>
        <Modal v-model="editPasswordModal" :width="500">
            <h3 slot="header" style="color:#2D8CF0">修改密码</h3>
            <Form ref="editPasswordForm" :model="editPasswordForm" :label-width="100" label-position="right"
                  :rules="passwordValidate">
                <FormItem label="原密码" prop="oldPassword" :error="oldPassError">
                    <Input v-model="editPasswordForm.oldPassword" placeholder="请输入现在使用的密码"></Input>
                </FormItem>
                <FormItem label="新密码" prop="password">
                    <Input v-model="editPasswordForm.password" placeholder="请输入新密码，至少6位字符"></Input>
                </FormItem>
                <FormItem label="确认新密码" prop="password_confirmation">
                    <Input v-model="editPasswordForm.password_confirmation" placeholder="请再次输入新密码"></Input>
                </FormItem>
            </Form>
            <div slot="footer">
                <Button type="text" @click="editPasswordModal = false">取消</Button>
                <Button type="primary" :loading="savePassLoading" @click="saveEditPass">保存</Button>
            </div>
        </Modal>
    </div>
</template>
<script>
    import Cookies from 'js-cookie';
    export default {
        name: 'ucenter',
        data() {
            const validePhone = (rule, value, callback) => {
                let re = /^1[0-9]{10}$/;
                if (!re.test(value)) {
                    callback(new Error('请输入正确格式的手机号'));
                } else {
                    callback();
                }
            };
            const valideRePassword = (rule, value, callback) => {
                if (value !== this.editPasswordForm.password) {
                    callback(new Error('两次输入密码不一致'));
                } else {
                    callback();
                }
            };
            return {
                uploadHeaders:{
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
                },
                uploadUrl: 'api/ucenter/avatar',
                userForm: {
                    id: '',
                    username: '',
                    nickname: '',
                    real_name: '',
                    mobile: '',
                    email: '',
                    avatar: '',
                    department: ''
                },
                save_loading: false,
                identifyError: '', // 验证码错误
                editPasswordModal: false, // 修改密码模态框显示
                savePassLoading: false,
                oldPassError: '',
                checkIdentifyCodeLoading: false,
                inforValidate: {
                    name: [
                        {required: true, message: '请输入姓名', trigger: 'blur'}
                    ],
                    cellphone: [
                        {required: true, message: '请输入手机号码'},
                        {validator: validePhone}
                    ]
                },
                editPasswordForm: {
                    oldPassword: '',
                    password: '',
                    password_confirmation: ''
                },
                passwordValidate: {
                    oldPassword: [
                        {required: true, message: '请输入原密码', trigger: 'blur'}
                    ],
                    password: [
                        {required: true, message: '请输入新密码', trigger: 'blur'},
                        {min: 5, message: '请至少输入5个字符', trigger: 'blur'},
                        {max: 32, message: '最多输入32个字符', trigger: 'blur'}
                    ],
                    password_confirmation: [
                        {required: true, message: '请再次输入新密码', trigger: 'blur'},
                        {validator: valideRePassword, trigger: 'blur'}
                    ]
                },
            };
        },
        mounted() {
            this.userForm = this.$store.getters.userInfo;
        },
        methods: {
            showEditPassword() {
                this.editPasswordModal = true;
            },
            cancelEditUserInfor() {
                this.$store.commit('removeTag', 'ownspace_index');
                localStorage.pageOpenedList = JSON.stringify(this.$store.state.app.pageOpenedList);
                let lastPageName = '';
                if (this.$store.state.app.pageOpenedList.length > 1) {
                    lastPageName = this.$store.state.app.pageOpenedList[1].name;
                } else {
                    lastPageName = this.$store.state.app.pageOpenedList[0].name;
                }
                this.$router.push({
                    name: lastPageName
                });
            },
            saveEdit() {
                this.$refs['userForm'].validate((valid) => {
                    if (valid) {
                        this.$messageHttp.put('/api/ucenter/profile', this.userForm,
                            (result) => {
                                sessionStorage.setItem('userInfo', JSON.stringify(result.data));
                                this.$store.commit('setUserInfo', result.data);
                            }
                        )
                    }
                });
            },
            saveEditPass() {
                this.$refs['editPasswordForm'].validate((valid) => {
                    if (valid) {
                        this.savePassLoading = true;
                        this.$messageHttp.put('/api/ucenter/password', this.editPasswordForm,
                            () => this.editPasswordModal = false,
                            null,
                            () => this.savePassLoading = false
                        );
                    }
                });
            },
            uploadSuccess (res) {
                this.userForm.avatar = res.data;
                this.$refs.upload.clearFiles();
            },
        },
    };
</script>
<style lang="less">
    .own-space{
        &-btn-box{
            margin-bottom: 10px;
            button{
                padding-left: 0;
                span{
                    color: #2D8CF0;
                    transition: all .2s;
                }
                span:hover{
                    color: #0C25F1;
                    transition: all .2s;
                }
            }
        }
        &-tra{
            width:10px;
            height:10px;
            transform:rotate(45deg);
            position:absolute;
            top:50%;
            margin-top:-6px;
            left:-3px;
            box-shadow:0 0 2px 3px rgba(0,0,0,.1);
            background-color:white;z-index:100;
        }
        &-input-identifycode-con{
            position:absolute;
            width:200px;
            height:100px;
            right:-220px;
            top:50%;
            margin-top:-50px;
            border-radius:4px;
            box-shadow:0 0 2px 3px rgba(0,0,0,.1);
        }
    }
</style>
