<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Git Client For PHP</title>

    <link rel="stylesheet" href="{{ url('_tool/assets/css/bootstrap.min') }}" />
    <link rel="stylesheet" href="{{ url('_tool/assets/css/bootstrap-theme.min') }}" />
    <link rel="stylesheet" href="{{ url('_tool/assets/css/site') }}" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{{ url('_tool/assets/javascript/html5shiv.min') }}"></script>
    <script src="{{ url('_tool/assets/javascript/respond.min') }}"></script>
    <![endif]-->
</head>
<body>
<header class="site-header jumbotron">
    <div class="site-nav"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <p>Git Client For PHP</p>
            </div>
        </div>

        <div class="row">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">GIT 库</a>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav" id="repo-list"></ul>
                        <ul class="nav navbar-nav navbar-right"></ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
    </div>
</header>
<div class="container">
    <div class="jumbotron branch">
        <h3 class="package-amount left" id="">New Branches </h3>
        <div id="message-container"></div>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="请输入新的分支名称">
          <span class="input-group-btn">
            <button class="btn btn-default" type="button" id="new-branch">Go!</button>
          </span>
        </div>
        <div>
            <h3 class="package-amount left" id="">Active Branch  <span class="text-success" id="active-branch"></span> </h3>
        </div>
        <div>
            <h3 class="package-amount left" id="">Local Branches</h3>
            <div class="row" id="branches"></div>
        </div>
        <div>
            <h3 class="package-amount left" id="">Remote Branches</h3>
            <div class="row" id="remote-branches"></div>
        </div>
    </div>
    <div class="jumbotron status">
        <h3 class="package-amount left" id="">Status </h3>
        <span id="status"></span>
    </div>
</div>

<script type="text/javascript" src="{{ url('_tool/assets/javascript/jquery.1.11.3.min') }}"></script>
<script type="text/javascript" src="{{ url('_tool/assets/javascript/bootstrap.min') }}"></script>

<script type="text/javascript">

    var _prefix = '{{ config('phpgit.route_prefix') }}/git';

    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(function(){
        page.init();
    });

    var page = page || {};

    page = {
        repoListDom: $('#repo-list'),
        statusDom: $('#status'),
        branchDom: $('.branch'),
        branchesDom: $('#branches'),
        remoteBranchesDom: $('#remote-branches'),
        activeBranchDom: $('#active-branch'),

        newBranchBtnDom: $('#new-branch'),

        init: function(){
            this.initRepoList(), this.initBranches(), this.addEvent()
        },
        initRepoList: function(){
            var self = this;
            this.ajaxGet(_prefix + '/repo-list', this.urlParam(), function(data){
                $.each(data.rows, function(key, value) {
                    var style = '';
                    if(value = data.current){
                        style = 'active';
                    }
                    var url = self.rootUrl() + _prefix + '?repo=' + value;
                    self.repoListDom.append('<li class="' + style + '"><a href="' + url + '">' + key + '</a></li>');
                });
            });
        },
        initBranches: function(){
            var self = this;
            this.ajaxGet(_prefix + '/branches', this.urlParam(), function(data){
                self.branchesDom.html('');
                self.statusDom.html(data.status);

                $.each(data.rows, function(i, value){
                    if(value.indexOf('*') > -1){
                        self.activeBranchDom.html(value);
                        return true;
                    }
                    self.branchesDom.append(
                            '<div class="panel panel-default pull-left">'+value+'&nbsp;&nbsp;&nbsp;\
                        <button type="button" data-branch="'+value+'" class="btn btn-danger btn-default pull-right delete">X</button>\
                        <button type="button" data-branch="'+value+'" class="btn btn-success btn-default pull-right checkout">active</button>\
                        </div>'
                    );
                });
            });

            this.ajaxGet(_prefix + '/remote-branches', this.urlParam(), function(data){

                self.remoteBranchesDom.html('');
                $.each(data.rows, function(i, value){
                    self.remoteBranchesDom.append(
                            '<div class="panel panel-default pull-left">'+value+'&nbsp;&nbsp;&nbsp;\
                        <button type="button" data-branch="'+value+'" class="btn btn-success btn-default pull-right checkout">active</button>\
                        </div>'
                    );
                });
            });
        },
        checkout: function(branch){
            var self = this;
            var param = $.extend(true, this.urlParam(), {
                branch: branch
            });

            this.ajaxPost(_prefix + '/checkout', param, function(){
                self.initBranches();
            });
        },
        remoteCheckout: function(branch){
            var self = this;
            var param = $.extend(true, this.urlParam(), {
                branch: branch
            });

            this.ajaxPost(_prefix + '/remote-checkout', param, function(){
                self.initBranches();
            });
        },
        deleteBranch: function(branch){
            var self = this;
            var param = $.extend(true, this.urlParam(), {
                branch: branch
            });

            this.ajaxPost(_prefix + '/delete', param, function(){
                self.initBranches();
            });
        },
        addEvent: function(){
            var self = this;

            this.newBranchBtnDom.on('click', function(){
                var branch = $(this).parents('.input-group').find('input').val();
                $(this).parents('.input-group').find('input').val('');
                self.checkout(branch);
            });

            this.branchDom.on('keydown', 'input', function(e){
                var ev = document.all ? window.event : e;
                if(ev.keyCode==13) {
                    var branch = $(this).val();
                    $(this).val('');
                    self.checkout(branch);
                }
            });

            this.branchesDom.on('click', '.checkout', function(){
                self.checkout($(this).data('branch'));
            });

            this.branchesDom.on('click', '.delete', function(){
                self.deleteBranch($(this).data('branch'));
            });

            this.remoteBranchesDom.on('click', '.checkout', function(){
                self.remoteCheckout($(this).data('branch'));
            });
        },

        ajaxGet: function(url, params, callback){
            var self =this;
            self.activeBranchDom.html('waiting... ');

            $.ajax({
                type: "GET",
                url: self.rootUrl() + url,
                dataType: "json",
                data: params,
                async: (typeof(params.async) == 'undefined') ? true : params.async,
                success: function(data, status){
                    if(data.code == 200) {
                        return callback(data);
                    }else{
                        self.alert('CODE: ' + data.code + ':' + data.msg, 'danger');
                        return false;
                    }
                },
                error: function(err){
                    $('.loading').hide();
                    var errData = {'code': 500, 'msg': '服务器内部错误'};
                    if(err.status == 422 && typeof(err.responseJSON) !== "undefined"){
                        errData = $.extend(true, {'code': 422, 'msg': '输入参数有误', 'errData': err.responseJSON });
                    }else if(err.status == 403) {
                        errData = {'code': 403, 'msg': '禁止访问！'};
                    }
                    else if(typeof(err.responseJSON) !== "undefined"){
                        errData = err.responseJSON;
                    }

                    self.alert('CODE: ' + errData.code + ':' + errData.msg, 'danger');
                }
            });
        },

        ajaxPost: function(url, params, callback){
            var self =this;
            self.activeBranchDom.html('waiting... ');

            $.ajax({
                type: "POST",
                url: self.rootUrl() + url,
                dataType: "json",
                data: params,
                async: (typeof(params.async) == 'undefined') ? true : params.async,
                success: function(data, status){
                    if(data.code == 200) {
                        return callback(data);
                    }else{
                        self.alert('CODE: ' + data.code + ':' + data.msg, 'danger');
                        return false;
                    }
                },
                error: function(err){
                    $('.loading').hide();
                    var errData = {'code': 500, 'msg': '服务器内部错误'};
                    if(err.status == 422 && typeof(err.responseJSON) !== "undefined"){
                        errData = $.extend(true, {'code': 422, 'msg': '输入参数有误', 'errData': err.responseJSON });
                    }else if(err.status == 403) {
                        errData = {'code': 403, 'msg': '禁止访问！'};
                    }
                    else if(typeof(err.responseJSON) !== "undefined"){
                        errData = err.responseJSON;
                    }

                    self.alert('CODE: ' + errData.code + ':' + errData.msg, 'danger');
                }
            });
        },

        alert: function(message, type){
            $('#message-container').append('\
            <div class="alert alert-'+type+' alert-dismissible" role="alert">\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
             ' + message + '</div>');
        },
        urlParam: function() {
            var param, url = location.search, theRequest = {};
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                strs = str.split("&");
                for(var i = 0, len = strs.length; i < len; i ++) {
                    param = strs[i].split("=");
                    theRequest[param[0]]=decodeURIComponent(param[1]);
                }
            }
            return theRequest;
        },
        rootUrl: function(){
            return location.href.substr(0, location.href.indexOf(location.pathname)) + '/';
        }
    };

</script>
</body>
</html>