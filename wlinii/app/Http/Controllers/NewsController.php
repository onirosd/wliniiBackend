<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $newsList = DB::table('Noticias')
                    ->orderBy('FechaCreacion', 'ASC')
                    ->get();

        return view('news', ['newsList' => $newsList]);
    }
    public function newsDetail($id = null)
    {   
        if($id){
            $news = DB::table('Noticias')
                    ->where('IdNoticias', $id)
                    ->first();
        }else{
            $news = null;
        }

        return view('news_details', ['news' => $news]);
    }

    public function saveNews(Request $request)
    {   
        $IdNoticias = $request->input('id');
        $data = array(
            'Titulo' => $request->input('title'),
            'SubTitulo' => $request->input('subtitle'),
            'Descripcion' => $request->input('description'),
            'Estado' => $request->input('state'),
            'Image' => $request->input('img-url'),
            'FechaCurso' => $request->input('fechacurso')
        );

        if($IdNoticias){
            DB::table('Noticias')->where('IdNoticias', $IdNoticias)
                ->update($data);
        }else{
            $date = date("Y-m-d");
            $data['FechaCreacion'] = $date;
            DB::table('Noticias')->insert($data);
        };

        return redirect()->route('news');
    }

    public function deleteNews(Request $request, $id)
    {       
        if($id){
            DB::table('Noticias')->where('IdNoticias', $id)
                ->delete();
        }

        return redirect()->route('news');
    }
}