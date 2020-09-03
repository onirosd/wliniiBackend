<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('js/bootstrap.min.js') }}" rel="stylesheet">
<link href="{{ asset('js/jquery.min.js') }}" rel="stylesheet">
<link href="{{ asset('css/main.css') }}" rel="stylesheet">
<!------ Include the above in your HEAD tag ---------->

<link href='http://fonts.googleapis.com/css?family=Raleway:400,200' rel='stylesheet' type='text/css'>

<div class="container">
	<div class="row login_box">
	    <div class="col-md-12 col-xs-12" align="center">
            <div class="line"><h3>12 : 30 AM</h3></div>
            <div class="outter"><img src="{{ asset('img/people-q-c-100-100-1.jpg') }}" class="image-circle"/></div>   
            <h1>Hi Guest</h1>
            <span>Wiliin</span>
        </div>       
        <div class="col-md-12 col-xs-12 login_control">
            {{ Form::open(['action' => 'LoginController@login','method' => 'post']) }}
            {{ Form::token() }}
                <div class="control">
                    {{ Form::label('user','Usuario',['class' =>'label']) }}
                    {{ Form::text('user','',['class' =>'form-control']) }}
                </div>
                
                <div class="control">
                    {{ Form::label('password','Contraseña',['class' =>'label']) }}
                    {{ Form::password('password',['class' =>'form-control']) }}
                </div>
                <div align="center">
                     <button class="btn btn-orange">LOGIN</button>
                </div>
                
        </div>
        {{ Form::close() }} 
    </div>
</div>