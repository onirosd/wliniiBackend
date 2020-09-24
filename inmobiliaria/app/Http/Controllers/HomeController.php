<?php

namespace App\Http\Controllers;

use App\Models\TipoInmueble;
use App\Models\EstadoPublicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 
        $this->storage_path = base_path().'/public';
    }

    public function index(Request $request)
    {
        // 
    }

    public function getFilterOptions()
    {
        $operationTypes = DB::table('tipooperacion')->get();
        $inmuebleTypes = TipoInmueble::with('properties')->get();
        $locations = DB::table('ubigeo')->get();
        
        return response()->json([
            'operation_types' => $operationTypes,
            'inmueble_types' => $inmuebleTypes,
            'locations' => $locations
        ]);
    }

    public function getGeneralOptions()
    {
        $operationTypes = DB::table('tipooperacion')->get();
        $inmuebleTypes = TipoInmueble::with('properties')->get();
        $comisionTypes = DB::table('tipocomision')->get();
        $monedaTypes = DB::table('tipomoneda')->get();
        $locations = DB::table('ubigeo')->get();
        $pub_states = EstadoPublicacion::get();
        
        return response()->json([
            'operation_types' => $operationTypes,
            'inmueble_types' => $inmuebleTypes,
            'comision_types' => $comisionTypes,
            'moneda_types' => $monedaTypes,
            'locations' => $locations,
            'pub_states' => $pub_states
        ]);
    }

    public function getCountries()
    {
        $countries = DB::table('pais')->orderBy('Des_Pais', 'asc')->get();
        return response()->json($countries);
    }

    public function getNewsById(Request $request, $id)
    {
        $news = DB::table('noticias')
                ->where('Estado', 1)
                ->where('IdNoticias', $id)                
                ->orderBy('FechaCreacion', 'ASC')
                ->get();
        return response()->json($news);
    }


     public function getNews()
    {
        $news = DB::table('noticias')
                ->where('Estado', 1)                
                ->orderBy('FechaCreacion', 'ASC')
                ->get();
        return response()->json($news);
    }

    public function fileUpload(Request $request){
        $this->validate($request, [
            'file' => 'required',
            'file.*' => 'mimes:jpg,jpeg,png'
        ]);

        if($request->hasfile('file')){
            $file = $request->file('file');
            $filename = time().'.'.$file->extension();
            $file->move($this->storage_path.'/images/upload/', $filename);
            $des_fileUrl = "/images/upload/".$filename;
        }

        return response()->json([
            'status' => 'success',
            'url' => $des_fileUrl
        ]);
    }
}
