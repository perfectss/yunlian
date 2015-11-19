<!-- begin #page-container -->
<div id="page-container" class="fade">
    <!-- begin login -->
    <div class="login bg-black animated fadeInDown">
        <!-- begin brand -->
        <div class="login-header">
            <div class="brand">
                <span class="logo"></span> 期待你的加入
                <small>人生是需要等待的</small>
            </div>
            <div class="icon">
                <i class="fa fa-sign-in"></i>
            </div>
        </div>
        <!-- end brand -->
        <div class="login-content">
            <form action="<?php echo site_url('reg_login/login');?>" method="POST" class="margin-bottom-0" onsubmit="return Dcheck();">
                <div class="form-group m-b-20">
                    <input type="email" id="username" name="username" class="form-control input-lg" id="exampleInputEmail2" placeholder="电子邮件地址" />
                </div>
                <div class="form-group m-b-20">
                    <input type="password"  id="password" name="password" class="form-control input-lg" placeholder="密码" />
                </div>
                <div class="checkbox m-b-20">
                    <label>
                        <input type="checkbox" name="checkbox" checked="true"/> 记住我
                    </label>
                </div>
                <div class="login-buttons">
                    <button type="submit" class="btn btn-success btn-block btn-lg">登陆</button>
                </div>
            </form>
        </div>
    </div>
    <!-- end login -->
</div>
<!-- end page container -->
<script>
    $(function(){
        var password = $("#password");
        var username = $("#username");
        if(password == null) alert('看不到密码输入框？ 请检查file/cache目录是否可写');
        if(username.val() == '') {
            username.focus();
        } else {
            password.focus();
        }
    });
    function Dcheck() {
        var password = $("#password");
        var username = $("#username");
        if(username.val()  == '') {
            confirm('请填写会员名');
            username.focus();
            return false;
        }
        if(password.val() == '') {
            confirm('请填写密码');
            password.focus();
            return false;
        }
        return true;
    }
</script>

