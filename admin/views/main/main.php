
<!-- begin #page-container -->
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <?php include_once('/../public/header.php');?>
    <?php include_once('/../public/left.php');?>
    <!-- begin #content -->
    <div id="content" class="content">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li><a href="javascript:;">我的面板</a></li>
            <li class="active">系统首页</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">主页 <small>请尽请期待...</small></h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-green">
                    <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
                    <div class="stats-title">今天的访问</div>
                    <div class="stats-number">7,842,900</div>
                    <div class="stats-progress progress">
                        <div class="progress-bar" style="width: 70.1%;"></div>
                    </div>
                    <div class="stats-desc">比上星期好 (70.1%)</div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-blue">
                    <div class="stats-icon stats-icon-lg"><i class="fa fa-tags fa-fw"></i></div>
                    <div class="stats-title">今天的利润</div>
                    <div class="stats-number">180,200</div>
                    <div class="stats-progress progress">
                        <div class="progress-bar" style="width: 40.5%;"></div>
                    </div>
                    <div class="stats-desc">比上星期好 (40.5%)</div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-purple">
                    <div class="stats-icon stats-icon-lg"><i class="fa fa-shopping-cart fa-fw"></i></div>
                    <div class="stats-title">新订单</div>
                    <div class="stats-number">38,900</div>
                    <div class="stats-progress progress">
                        <div class="progress-bar" style="width: 76.3%;"></div>
                    </div>
                    <div class="stats-desc">比上星期好 (76.3%)</div>
                </div>
            </div>
            <!-- end col-3 -->
            <!-- begin col-3 -->
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-black">
                    <div class="stats-icon stats-icon-lg"><i class="fa fa-comments fa-fw"></i></div>
                    <div class="stats-title">新评论</div>
                    <div class="stats-number">3,988</div>
                    <div class="stats-progress progress">
                        <div class="progress-bar" style="width: 54.9%;"></div>
                    </div>
                    <div class="stats-desc">比上星期好 (54.9%)</div>
                </div>
            </div>
            <!-- end col-3 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end #content -->

    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
</div>
<!-- end page container -->
<script src="<?php echo base_url('/skin/plugins/gritter/js/jquery.gritter.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/flot/jquery.flot.min.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/flot/jquery.flot.time.min.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/flot/jquery.flot.resize.min.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/flot/jquery.flot.pie.min.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/sparkline/jquery.sparkline.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.min.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/jquery-jvectormap/jquery-jvectormap-world-mill-en.js')?>"></script>
<script src="<?php echo base_url('/skin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')?>"></script>
<script src="<?php echo base_url('/skin/js/dashboard.min.js')?>"></script>