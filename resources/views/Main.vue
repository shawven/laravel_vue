<template>
    <div class="main" :class="{'main-hide-text': shrink}">
        <div class="sidebar-menu-con" :style="{width: shrink?'60px':'200px', overflow: shrink ? 'visible' : 'auto'}">
            <shrinkable-menu
                    :shrink="shrink"
                    @on-change="handleSubmenuChange"
                    :theme="menuTheme"
                    :before-push="beforePush"
                    :open-names="openedSubmenuArr"
                    :menu-list="menuList">
                <div slot="top" class="logo-con m-0">
                    <div class="w-100">
                        <Button icon="ios-home" class="font-2xl rounded-0 border-0" long
                                 :style="{height: '60px',paddingLeft: '20px',
                                  background: menuTheme === 'dark' ? '#495060' : '#fff',
                                  color: menuTheme === 'dark' ? '#fff' : '#495060'}"
                                 @click="$router.push({name: 'home_index'})">
                            <span v-if="!shrink" style="letter-spacing: 7px">疯狂彩票</span>
                        </Button>
                    </div>
                    <!--<img v-show="!shrink"  src="../images/logo.jpg" key="max-logo" />-->
                </div>
            </shrinkable-menu>
        </div>
        <div class="main-header-con" :style="{paddingLeft: shrink?'60px':'200px'}">
            <div class="main-header">
                <div class="navicon-con">
                    <Button :style="{transform: 'rotateZ(' + (this.shrink ? '-90' : '0') + 'deg)'}" type="text" @click="toggleClick">
                        <Icon type="ios-menu" size="32"></Icon>
                    </Button>
                </div>
                <div class="header-middle-con">
                    <div class="main-breadcrumb">
                        <breadcrumb-nav :currentPath="currentPath"></breadcrumb-nav>
                    </div>
                </div>
                <div class="header-avator-con">
                    <div class="user-dropdown-menu-con">
                        <Row type="flex" justify="end" align="middle" class="user-dropdown-innercon">
                            <message-tip v-model="mesCount"></message-tip>
                            <Dropdown transfer trigger="click" @on-click="handleClickUserDropdown">
                                <a href="javascript:void(0)">
                                    <span class="main-user-name text-center">{{username}}</span>
                                    <Icon type="ios-arrow-down"></Icon>
                                </a>
                                <DropdownMenu slot="list">
                                    <DropdownItem name="ownSpace">个人中心</DropdownItem>
                                    <DropdownItem name="loginout" divided>退出登录</DropdownItem>
                                </DropdownMenu>
                            </Dropdown>
                            <Avatar :src="avatarPath" style="background: #619fe7;margin-left: 10px;"></Avatar>
                        </Row>
                    </div>
                </div>
            </div>
            <div class="tags-con">
                <tags-page-opened :pageTagsList="pageTagsList"></tags-page-opened>
            </div>
        </div>
        <div class="single-page-con" :style="{left: shrink?'60px':'200px'}">
            <div class="single-page">
                <keep-alive>
                    <router-view></router-view>
                </keep-alive>
            </div>
        </div>
    </div>
</template>
<script>
    import shrinkableMenu from './main-components/shrinkable-menu/shrinkable-menu.vue';
    import tagsPageOpened from './main-components/tags-page-opened.vue';
    import breadcrumbNav from './main-components/breadcrumb-nav.vue';
    import messageTip from './main-components/message-tip.vue';
    import util from '@/libs/util';

    export default {
        components: {
            shrinkableMenu,
            tagsPageOpened,
            breadcrumbNav,
            messageTip,
        },
        data () {
            return {
                shrink: false,
                username: '',
                isFullScreen: false,
                openedSubmenuArr: this.$store.state.app.openedSubmenuArr
            };
        },
        computed: {
            avatarPath () {
                return this.$store.getters.userInfo.avatar;
            },
            menuList () {
                return this.$store.getters.grantedRoutes;
            },
            pageTagsList () {
                return this.$store.state.app.pageOpenedList; // 打开的页面的页面对象
            },
            currentPath () {
                return this.$store.state.app.currentPath; // 当前面包屑数组
            },
            cachePage () {
                return this.$store.state.app.cachePage;
            },
            lang () {
                return this.$store.state.app.lang;
            },
            menuTheme () {
                return this.$store.state.app.menuTheme;
            },
            mesCount () {
                return this.$store.state.app.messageCount;
            }
        },
        methods: {
            init () {
                let pathArr = util.setCurrentPath(this, this.$route.name);
                if (pathArr.length >= 2) {
                    this.$store.commit('addOpenSubmenu', pathArr[1].name);
                }
                this.username = this.$store.getters.userInfo.nickname;
                let messageCount = 3;
                this.messageCount = messageCount.toString();
                this.checkTag(this.$route.name);
                this.$store.commit('setMessageCount', 3);
            },
            toggleClick () {
                this.shrink = !this.shrink;
            },
            handleClickUserDropdown (name) {
                if (name === 'ownSpace') {
                    util.openNewPage(this, 'ownspace_index');
                    this.$router.push({name: 'ownspace_index'});
                }
                if (name === 'loginout') {
                    // 退出登录
                    this.$store.commit('clearOpenedSubmenu');
                    this.$store.commit('logout')
                }
            },
            checkTag (name) {
                let openpageHasTag = this.pageTagsList.some(item => {
                    if (item.name === name) {
                        return true;
                    }
                });
                if (!openpageHasTag) { //  解决关闭当前标签后再点击回退按钮会退到当前页时没有标签的问题
                    util.openNewPage(this, name, this.$route.params || {}, this.$route.query || {});
                }
            },
            handleSubmenuChange (val) {
                // console.log(val)
            },
            beforePush (name) {
                // if (name === 'accesstest_index') {
                //     return false;
                // } else {
                //     return true;
                // }
                return true;
            },
            fullscreenChange (isFullScreen) {
                // console.log(isFullScreen);
            }
        },
        watch: {
            '$route' (to) {
                this.$store.commit('setCurrentPageName', to.name);
                let pathArr = util.setCurrentPath(this, to.name);
                if (pathArr.length > 2) {
                    this.$store.commit('addOpenSubmenu', pathArr[1].name);
                }
                this.checkTag(to.name);
                sessionStorage.currentPageName = to.name;
            },
            lang () {
                util.setCurrentPath(this, this.$route.name); // 在切换语言时用于刷新面包屑
            }
        },
        mounted () {
            this.init();
        },
        created () {
            // 显示打开的页面的列表
            this.$store.commit('setOpenedList');
            this.$store.commit('initCachepage');
            this.$store.dispatch('initTagsList');
        }
    };
</script>

<style lang="less">
    .ivu-menu-dark {
        background: #495060;
    }
    .lock-screen-back{
        border-radius: 50%;
        z-index: -1;
        box-shadow: 0 0 0 0 #667aa6 inset;
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
        transition: all 3s;
    }
    .main{
        position: absolute;
        width: 100%;
        height: 100%;
        .unlock-con{
            width: 0px;
            height: 0px;
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 11000;
        }
        .sidebar-menu-con{
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 21;
            transition: width .3s;
        }
        .layout-text{
            display: inline-block;
            white-space:nowrap;
            position: absolute;
        }
        .main-hide-text .layout-text{
            display: none;
        }
        &-content-container{
            position: relative;
        }
        &-header-con{
            box-sizing: border-box;
            position: fixed;
            display: block;
            padding-left: 200px;
            width: 100%;
            height: 100px;
            z-index: 20;
            box-shadow: 0 2px 1px 1px rgba(100,100,100,.1);
            transition: padding .3s;
        }
        &-breadcrumb{
            padding: 8px 15px 0;
        }
        &-menu-left{
            background: #464c5b;
            height: 100%;
        }
        .tags-con{
            height: 40px;
            z-index: -1;
            overflow: hidden;
            background: #f0f0f0;
        }
        &-header{
            height: 60px;
            background: #fff;
            box-shadow: 0 2px 1px 1px rgba(100,100,100,.1);
            position: relative;
            z-index: 11;
            .navicon-con{
                margin: 6px;
                display: inline-block;
            }
            .header-middle-con{
                position: absolute;
                left: 60px;
                top: 0;
                right: 340px;
                bottom: 0;
                padding: 10px;
                overflow: hidden;
            }
            .header-avator-con{
                position: absolute;
                right: 0;
                top: 0;
                height: 100%;
                width: 300px;
                .switch-theme-con{
                    display: inline-block;
                    width: 40px;
                    height: 100%;
                }
                .message-con{
                    display: inline-block;
                    width: 30px;
                    padding: 18px 0;
                    text-align: center;
                    cursor: pointer;
                    i{
                        vertical-align: middle;
                    }
                }
                .change-skin{
                    font-size: 14px;
                    font-weight: 500;
                    padding-right: 5px;
                }
                .switch-theme{
                    height: 100%;
                }
                .user-dropdown{
                    &-menu-con{
                        position: absolute;
                        right: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        .main-user-name{
                            display: inline-block;
                            margin-left: 16px;
                            word-break: keep-all;
                            white-space: nowrap;
                            vertical-align: middle;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            text-align: right;
                        }
                    }
                    &-innercon{
                        height: 100%;
                        padding-right: 14px;
                    }
                }
                .full-screen-btn-con{
                    display: inline-block;
                    width: 30px;
                    padding: 18px 0;
                    text-align: center;
                    cursor: pointer;
                    i{
                        vertical-align: middle;
                    }
                }
                .lock-screen-btn-con{
                    display: inline-block;
                    width: 30px;
                    padding: 18px 0;
                    text-align: center;
                    cursor: pointer;
                    i{
                        vertical-align: middle;
                    }
                }
            }
        }
        .single-page-con{
            position: absolute;
            top: 100px;
            right: 0;
            bottom: 0;
            overflow: auto;
            background-color: #F0F0F0;
            z-index: 1;
            transition: left .3s;
            .single-page{
                margin: 10px;
            }
        }
        &-copy{
            text-align: center;
            padding: 10px 0 20px;
            color: #9ea7b4;
        }
    }
    .taglist-moving-animation-move{
        transition: transform .3s;
    }
    .logo-con{
        padding: 0;
        margin-bottom: -5px;
        img{
            width: 100%;
        }
    }
</style>
