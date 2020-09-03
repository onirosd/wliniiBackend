<?php if($user->ustype == 'message'){ ?>
    <h4>{{$user->message}}</h4>

<?php } elseif($user->ustype == 'contactanos') { ?>

	<h4>Hola Administrador :	</h4> <br>
	<h4>Tienes un nuevo mensaje de contacto de parte de {{$user->nombrecompleto}} , con correo : {{$user->correo}} .</h4>
	<h4>El mensaje es el siguiente : </h4>
	<h6>
		
         {{$user->message}}

	</h6>

<?php } elseif($user->ustype == 'recuperar') { ?>

	<h3>Hola Estimado {{$user->name}} :	</h3> <br>
	<h3>Se ah cambiado su contraseña de acceso a la plataforma de wlinii, segun se solicito.</h3>
	<h4>Las credenciales son las siguientes : </h4>
	<h4>
		
		Usuario    : {{$user->user}} <br>
		Contraseña : {{$user->pass}} <br>
	</h4>

<?php } else { ?>
    <?php if($user->ustype == 1 || $user->ustype == 3){ ?>
    <h4>Wlinii te da la Bienvenida a la primera herramienta Web para Agentes Inmobiliarios hecha por Agentes Inmobiliarios.</h4>


    <?php } else { ?>
    {{-- <h4>Wlinii le informa que algunos asesores de su equipo de ventas quieren registrarse en nuestra plataforma web. <br> Para poder completar el ingreso de estos asesores requerimos su registro gratuito como administrador para que usted los apruebe.</h4> --}}
    <h4>El asesor {{$user->fullname}} fue afiliado con exito, sus credenciales son las siguientes:</h4>
    <?php } ?>
    Usuario: <b>{{$user->name}}</b> <br>
    Contraseña: <b>{{$user->pass}}</b> 
    
    <b>Nota: Le recomendamos al ingresar cambiar la contraseña a una que sea fácil de recordar para usted.</b>
    <p>Atte.</p>
    <p>Wlinii</p>

<?php } ?>