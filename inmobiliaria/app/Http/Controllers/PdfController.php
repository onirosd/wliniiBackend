<?php 

namespace App\Http\Controllers;

use \Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

/**
* Pdf Controller
*/
class PdfController extends Controller
{
    
    public function generatePublicationPdf($id)
    {
        $publication = DB::table('publicacioncabecera as p')
                        ->leftJoin('ubigeo as u', 'u.IdUbigeo', 'p.IdUbigeo')                
                        ->leftJoin('tipoinmueble as t', 't.IdTipoInmueble', 'p.IdTipoInmueble')
                        ->leftJoin('tipooperacion as o', 'o.IdTipoOperacion', 'p.IdTipoOperacion')
                        ->where('IdPubCabecera', $id)
                        ->select(
                            'p.*',
                            't.Descripcion as tipo',
                            'o.Descripcion as operacion',
                            'u.FullText as ubicacion'
                        )
                        ->first();

        if(!$publication){
            return response()->json([
                'status' => 'fail',
                'message' => "can't find item"
            ], 404);
        }

        $persona = DB::table('persona as p')
                        ->join('usuario as u', 'u.IdPersonal', 'p.IdPersonal')
                        ->where('u.IdUsuario', $publication->IdUsuario)
                        ->select(
                            'p.*'
                        )->first();
        
        $images = DB::table('publicaciondetalleimagenes')
                        ->where('IdPubCabecera', $id)
                        ->get();

        $coord = $publication->Des_Coordenadas;
        if($coord){
            $mapUrl = 'map.png';
            $coord = json_decode($coord);
            $lat = $coord->lat;
            $lng = $coord->lng;    

            if($publication->Flg_MostrarDireccion){
                $options = array(
                    "center" => $lat.','.$lng,
                    "zoom" => '12',
                    'size' => '500x400',
                    'markers' => 'color:red|'.$lat.','.$lng,
                    "key" => 'AIzaSyCrfoNwsy3VVdbmuO9lr8ITavPXX5l78HI'
                );
                $isMarker = true;
            }else{
                $options = array(
                    "center" => $lat.','.$lng,
                    "zoom" => '12',
                    'size' => '500x400',
                    "key" => 'AIzaSyCrfoNwsy3VVdbmuO9lr8ITavPXX5l78HI'
                );
                $isMarker = false;
            }

            $url = "http://maps.googleapis.com/maps/api/staticmap?";
            $url .= http_build_query($options, '', '&');

            try{
                $data = file_get_contents($url);
                file_put_contents(base_path().'/../../appservice/map.png', $data);
                $mapUrl = 'map.png';
            }catch(Exception $e){
                $mapUrl = 'dummy_map.png';
            }
        }else{
            $mapUrl = 'dummy_map.png';
        }

        $info = array(
            'pubInfo' => $publication,
            'person' => $persona,
            'images' => $images,
            'mapUrl' => $mapUrl,
            'isMarker' => $isMarker,
        );

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('pdf_publication', $info)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'tempDir' => realpath(base_path()."/../../appservice"),
                'chroot' => realpath(base_path()."/../../appservice")
                ]);
        return $pdf->download('detalle.pdf');
    }

    public function generateAMCPDF(Request $request)
    {
        $user = Auth::guard('api')->user();
        $persona = DB::table('persona')
                        ->where('IdPersonal', $user->IdPersonal)
                        ->select('Des_NombreCompleto', 'Des_Telefono1', 'Des_Correo1')
                        ->first();

        $info = $request->all();
        $info['name'] = $persona->Des_NombreCompleto;
        $info['phone'] = $persona->Des_Telefono1;
        $info['email'] = $persona->Des_Correo1;
        $info['type'] = $user->Flg_TipoUsuario;

        $month = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $info['month'] = $month[ intval(date('m')) - 1 ];
        $info['day'] = intval(date('d'));

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('pdf_amc', $info);
        return $pdf->download('analisis.pdf');
    }
}
