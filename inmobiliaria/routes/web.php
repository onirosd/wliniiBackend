<?php

// use App\Models\Usuario;
// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

 $router->get('/', function () use ($router) {
    return $router->app->version();
 });
 $router->get('/serviciousuario', 'PersonaController@getAllPersonas');

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('usuario', ['uses' => 'UsuarioController@login']);
    $router->post('solicitud', ['uses' => 'SolicitudController@crearSolicitud']);

    $router->get('home/home_options', 'HomeController@getFilterOptions');
    $router->get('home/general_options', 'HomeController@getGeneralOptions');
    $router->get('home/countries', 'HomeController@getCountries');
    $router->get('home/noticias', 'HomeController@getNews');
    $router->get('home/noticias/{id}', 'HomeController@getNewsById');
    $router->post('upload', 'HomeController@fileUpload');
    
    // Inmuebles APIs about user without log in
    $router->get('inmuebles', 'InmuebleController@index');
    $router->get('inmuebles/mapa', 'InmuebleController@index');
    $router->get('inmuebles/featured[/{personid}]', ['uses' => 'InmuebleController@featured', 'as' => 'featured']);
    $router->get('inmuebles/latest[/{personid}]', ['uses' => 'InmuebleController@latest', 'as' => 'latest']);

    $router->get('resumenagentes', 'PersonaController@getResumenAgentes');
    $router->get('resumenagente/{id}', 'PersonaController@getResumenAgenteById');
    $router->get('resumenagenteByPublicacion/{id}', 'PersonaController@getResumenAgenteByPublication');
    $router->get('agenteByCode/{code}', 'PersonaController@findAgenteByCode');
    $router->get('agente/codigos', 'PersonaController@getAgenteCodes');
    $router->post('send_email_contactanos', 'NotificationController@send_email_contactanos');
    

    // AUTH APIs
        $router->post('usuario/recuperar', 'UsuarioController@recuperarPassword');

    $router->group(['middleware' => 'auth:api'], function () use ($router) {

        $router->post('send_email', 'NotificationController@sendEmail');
       


        $router->get('download_detail/{id}', 'PdfController@generatePublicationPdf');
        $router->post('perfil/image', 'PersonaController@addProfileImage');  // Upload profile image for Publication

        // Currency APIs
        $router->get('currency', 'CurrencyController@index');
        
        // AUTH APIs
        $router->post('usuario/changepassword', 'UsuarioController@ChangePassword');

        // Personal APIs
        $router->get('persona/names', 'PersonaController@getPersonalNames'); // User's Name List
        $router->get('persona', 'PersonaController@getPersonalInfo'); // Get User's Personal Info
        $router->put('persona', 'PersonaController@updatePersonalInfo'); // Update User's Personal Info

        // Notifications APIs
        $router->get('notifications/unread_count', 'NotificationController@UnReadCount');  // count of UnRead notifications
        $router->get('notifications/unread', 'NotificationController@UnReadNotifications');  // List of UnRead notifications
        $router->get('notifications', 'NotificationController@index');  // List of Notifications by User
        $router->put('notification/{notifyId}', 'NotificationController@UpdateNotificacion');  // List of Notifications by User

        $router->group(['middleware' => 'type:agente'], function () use ($router) {
            // Agente APIs
            $router->get('myagente', 'PersonaController@getResumenAgente');

            // Inmuebles APIs
            $router->get('inmuebles/myfeatured', 'InmuebleController@myfeatured');
            $router->get('inmuebles/mylatest', 'InmuebleController@mylatest');

            // Subscription APIs
            $router->get('subscription/types', 'SuscripcionController@getSuscripcionTypes');
            $router->post('subscription', 'SuscripcionController@createSuscripcion');
            $router->post('trial_subscription', 'SuscripcionController@trialSuscripcion');

            $router->group(['middleware' => 'suscrito'], function () use ($router) {
                // PUBLICACION APIs
                $router->get('publications', 'PublicationController@index');  // List of Publications by User
                $router->get('publication/{publication_id}', 'PublicationController@publicationByID'); // A Publication By Id
                $router->post('publication', 'PublicationController@createPublication');  // Create a Publication
                $router->put('publication/{publication_id}', 'PublicationController@updatePublication');  // update a Publication
                $router->delete('publication/image/{imageId}', 'PublicationController@removeImage'); // remove a image of Publication
                $router->post('publication/images', 'PublicationController@addImages');  // Upload some images for Publication
                $router->put('publicationdetalle', 'PublicationController@updatePublicacionDetalle'); // Publication's Deatail

                $router->get('publication/detail/{publication_id}', 'PublicationController@publicationDetailByID');
    
                // AMC APIs
                $router->get('amc/publications', 'AMCController@index');  // List of Publications by User
                $router->post('/amc/pdf', 'PdfController@generateAMCPDF');  // List of Publications by User

                // Suscription Check
                $router->get('subscription/check', function(){
                    return response()->json([
                        'status' => 'success'
                    ]);
                });
            }); 
        });

        $router->group(['middleware' => 'type:broker'], function () use ($router) {
            $router->get('broker/profil', 'PersonaController@getResumenAgenteByBroker'); // person profile
            $router->get('broker/related_pesonas', 'PersonaController@getRelatedPersonas'); // realted person List
            $router->get('broker/persona', 'PersonaController@getBrokerPersonalInfo'); // Get Broker's Personal Info
            $router->put('broker/persona', 'PersonaController@updateBrokerPersonalInfo'); // Update Broker's Personal Info
            $router->get('broker/publications', 'PublicationController@getPublicationsByBroker');  // List of Publications by Broker
        });

        
        


        //_________SOLICITUD
        $router->get('solicitudes', ['uses' => 'SolicitudController@mostrarSolicitudes']);
        //_________BROKER
        $router->get('brokerprofile/{id}', ['uses' => 'SolicitudController@mostrarBrokerSolicitudes']);
        $router->get('brokerprofile/{id}/{act}', ['uses' => 'SolicitudController@update']);
        //_________AFILIACION Y DESAFILIACION
        $router->put('afilia/{id}/{staf}', ['uses' => 'AfiliaController@afiliarPersona']);
        //_________PERSONA-PERSONARELACION_HIST
        $router->get('personahist/{id}', ['uses' => 'PersonaController@mostrarPersonaHist']);
    });
});

