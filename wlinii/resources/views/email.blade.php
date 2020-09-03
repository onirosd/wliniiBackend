<?php if($user->ustype == 1 || $user->ustype == 3){ ?>
<h4>Wlinii te da la bievenida a la plataforma web exclusiva para agentes inmobiliarios que la facilitara la búsqueda de propiedades permitiéndole aumentar la cantidad de cierre de operaciones inmobiliarias</h4>
<?php } else { ?>
{{-- <h4>Wlinii le informa que algunos asesores de su equipo de ventas quieren registrarse en nuestra plataforma web. <br> Para poder completar el ingreso de estos asesores requerimos su registro gratuito como administrador para que usted los apruebe.</h4> --}}
<h4>El asesor {{$user->fullname}} fue afiliado con exito, sus credenciales son las siguientes:</h4>
<?php } ?>
Usuario: <b>{{$user->name}}</b> <br>
Contraseña: <b>{{$user->pass}}</b> 