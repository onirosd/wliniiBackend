<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Valoracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    public function index(Request $request)
    {
        $userId = $this->authUser()->IdUsuario;

        if(!isset($_GET['count']) || !isset($_GET['page'])){
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid parameters count and page'
            ], 400);
        };

        $countPerPage = $_GET['count'];
        $offset = ( intval($_GET['page']) - 1) * $countPerPage;

        DB::table('Notificaciones')
            ->where('IdUsuario', $userId)
            ->orderBy('FechaCreacion', 'desc')
            ->offset($offset)
            ->limit($countPerPage)
            ->update(array('Flg_leer' => 1));

        $notifications = Notificacion::query()
                        ->join('Usuario as u', 'u.IdUsuario', '=', 'notificaciones.IdUsuarioRemitente')
                        ->join('Persona as p', 'p.IdPersonal', '=', 'u.IdPersonal')
                        ->leftJoin('Valoraciones as v', 'v.IdNotificacion', '=', 'notificaciones.IdNotificacion')
                        ->select(
                            'notificaciones.IdNotificacion',
                            'notificaciones.Des_Detalle',
                            'notificaciones.Flg_Tipo',
                            'notificaciones.Flg_Estado',
                            'notificaciones.FechaCreacion',
                            'p.Img_Personal',
                            'p.Des_NombreCompleto',
                            'v.Num_Valoracion'
                        )->where(
                            'notificaciones.IdUsuario', $userId
                        )->orderBy('notificaciones.FechaCreacion', 'desc');

        $total_count = $notifications->count();
        $data = $notifications->offset($offset)->limit($countPerPage)->get();
        
        return response()->json([
            'notifications' => $data,
            'total' => $total_count
        ]);
    }

    public function UnReadCount(Request $request){
        $userId = $this->authUser()->IdUsuario;

        $noti_count = DB::table('Notificaciones')
                        ->where([
                            ['IdUsuario', $userId],
                            ['Flg_Leer', 0]
                        ])->count();

        return response()->json($noti_count);
    }

    public function UnReadNotifications(Request $request)
    {
        $userId = $this->authUser()->IdUsuario;

        $notifications = DB::table('Notificaciones as n')
                        ->join('Usuario as u', 'u.IdUsuario', '=', 'n.IdUsuarioRemitente')
                        ->join('Persona as p', 'p.IdPersonal', '=', 'u.IdPersonal')
                        ->select(
                            'n.Des_Detalle',
                            'n.Flg_Tipo',
                            'n.FechaCreacion',
                            'p.Img_Personal',
                            'p.Des_NombreCompleto'
                        )->where([
                            ['n.IdUsuario', $userId],
                            ['n.Flg_Leer', 0]
                        ])->orderBy('n.FechaCreacion', 'desc');

        $count = $notifications->count();

        if(isset($_GET['count'])){
            $reqCount = $_GET['count'];
            $data = $notifications->limit($reqCount)->get();
        }else{
            $data = $notifications->get();
        }
        
        return response()->json([
            'notifications' => $data,
            'total' => $count
        ]);
    }

    public function UpdateNotificacion(Request $request, $notifyId){
        $userId = $this->authUser()->IdUsuario;
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $this->validate($request, [
            'valoracion' => 'required',
            // 'descripcion' => 'required'
        ]);

        $star = $request->input('valoracion');
        $desc = ""; //$request->input('descripcion');

        Notificacion::where('IdNotificacion', $notifyId)
                    ->where('IdUsuario', $userId)
                    ->update(['Des_Detalle' => $desc, 'Flg_Estado' => 1]);

        $notificacion = Notificacion::findOrFail($notifyId);
        $calificado = Usuario::where('IdUsuario', $notificacion->IdUsuarioRemitente)
                            ->select('IdPersonal')
                            ->first();
        $valoracion = array(
            'IdNotificacion' => $notifyId,
            'IdPersonal' => $calificado->IdPersonal,
            'IdPersonalCalificado' => $personId,
            'FechaCreacion' => date("Y-m-d H:i:s"),
            'Num_Valoracion' => $star
        );

        $oldValoracion = Valoracion::where('IdNotificacion', $notifyId)->first();
        if($oldValoracion){
            Valoracion::where('IdNotificacion', $notifyId)->update($valoracion);
        }else{
            Valoracion::insert($valoracion);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'updated successfully'
        ]);
    }

    public function sendEmail(Request $request){
        $senderInfo = $this->authUser();

        $this->validate($request, [
            'to' => 'required',
            'message' => 'required'
        ]);

        $receiverId = $request->input('to');
        $receiverInfo = Usuario::findOrFail($receiverId);
        $receiver = DB::table('Persona')
                            ->where('IdPersonal', $receiverInfo->IdPersonal)
                            ->select('Des_Correo1')->first();
        $sender = DB::table('Persona')
                            ->where('IdPersonal', $senderInfo->IdPersonal)
                            ->select('Des_Correo1')->first();

        $info = array(
            'sender_email' => $sender->Des_Correo1,
            'sender_name' =>  $senderInfo->NUsuario,
            'receiver_email' => $receiver->Des_Correo1,
        );
        $data = (object) $data = [ 
            'message' => $request->input('message'),
            'ustype' => 'message'
        ];

        Mail::send('email', ['user' => $data] , function($msj) use($info){
            $msj->from($info['sender_email'], $info['sender_name']);
            $msj->subject('Bienvenido a Wlinii');
            $msj->to($info['receiver_email']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Su correo electrónico ha sido enviado existosamente.'
        ]);
    }

    public function send_email_contactanos(Request $request){
        $senderInfo = $this->authUser();

        $this->validate($request, [
            //'to' => 'required',
            'message' => 'required',
            'nombrecompleto' => 'required',
            'correo' => 'required'
        ]);

       // $receiverId = $request->input('to');
       // $receiverInfo = Usuario::findOrFail($receiverId);
        $configuracion = DB::table('ConfigMaster')
                            ->where('CodConfigmaster', 1)
                            ->select('valor_ingresado')->first();
        // $sender = DB::table('Persona')
        //                     ->where('IdPersonal', $senderInfo->IdPersonal)
        //                     ->select('Des_Correo1')->first();

        $correo = $configuracion->valor_ingresado;
        $info   = array(
            'sender_email' => $correo,
            'sender_name' =>  'Administrador',
            'receiver_email' => $correo,
        );

        $data = (object) $data = [ 
            'message' => $request->input('message'),
            'nombrecompleto' => $request->input('nombrecompleto'),
            'correo' => $request->input('correo'),
            'ustype' => 'contactanos'
        ];

        Mail::send('email', ['user' => $data] , function($msj) use($info){
            $msj->from($info['sender_email'], $info['sender_name']);
            $msj->subject('Nuevo Mensaje de Contactanos');
            $msj->to($info['receiver_email']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Su correo electrónico ha sido enviado existosamente.'
        ]);
    }

}
