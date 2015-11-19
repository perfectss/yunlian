<!-- begin #page-container -->
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php include_once('header.php');?>
    <?php include_once('left.php');?>
    <!-- begin #content -->
    <div id="content" class="content">
        <!-- begin col-6 -->
        <div>
            <!-- begin panel -->
            <div class="panel panel-inverse" data-sortable-id="form-stuff-4">
                <div class="panel-heading-bg panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-default" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                    </div>
                    <h4 class="panel-title">修改密码</h4>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="<?php echo site_url('main/save_pwd');?>" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label">旧密码</label>
                                <div class="col-md-6">
                                    <input type="password" name="oldpassword" class="form-control" placeholder="Password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">新密码</label>
                                <div class="col-md-6">
                                    <input type="password" name="password" class="form-control" placeholder="Password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">确定密码</label>
                                <div class="col-md-6">
                                    <input type="password" name="cpassword" class="form-control" placeholder="Password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-sm btn-primary m-r-5">Login</button>
                                    <button type="submit" class="btn btn-sm btn-default">Cancel</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-6 -->
    </div>
    <!-- end #content -->
    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
    <input type="hidden" value="<?php if(!empty($msg)){ echo $msg;}?>" name="<?php if(!empty($code)){ echo $code;}?>" id="msg"/>
</div>
<?php if(1) {?>
<script type="text/javascript" language="javaScript">
    $(function(){
        var this_name = $("#msg");
        if(this_name.attr("name"))
        {
            alert(this_name.val());
            if (this_name.attr("name") == 3) {
                window.location.href = "";
            }
        }
    })
</script>
<?php }?>
