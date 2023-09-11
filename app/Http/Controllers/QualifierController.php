<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QualifierController extends Controller
{
    public $link, $api;

    public function __construct()
    {
        $this->link = config('app.api_link');
        $this->api = config('app.api_key');
    }

    public function index(Request $request){
        $type = $request->type;

        switch($type){
            case 'lists':
                return $this->lists($request);
            break;
            case 'enroll':
                return $this->save($request);
            break;
            case 'endorse':
                return $this->endorse($request);
            break;
            case 'insights':
                return $this->insights($request);
            break;
            default : 
            return inertia('Modules/Qualifiers/Index');
        }
    }

    public function store(Request $request){
        $type = $request->type;

        switch($type){
            case 'enroll':
                return $this->save($request);
            break;
            case 'endorse':
                return $this->endorse($request);
            break;
            case 'endorsed':
                return $this->endorsed($request);
            break;
        }
    }

    public function lists($request){
        $info = $request->info;
        $location = $request->location;
        $counts = $request->counts;
        $page = ($request->page) ? '&page='.$request->page : '';
        try{
            $url = $this->link.'/api/01101011%2001110010%2001100001%2001100100/qualifiers?lists=true&counts='.$counts.'&info='.$info.'&location='.$location.$page;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->api
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $datas = json_decode($response);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $datas;
    }

    public function insights($request){
        try{
            $url = $this->link.'/api/01101011%2001110010%2001100001%2001100100/qualifiers/insights';
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->api
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $datas = json_decode($response);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $datas;
    }

    public function save($request){
        $postData = array(
            'user' => $request->user,
            'school_id' => $request->school_id,
            'course_id' => $request->course_id,
            'account_no' => $request->account_no,
            'type' => $request->type,
        );

        try{
            $url = $this->link.'/api/01101011%2001110010%2001100001%2001100100/qualifiers';
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->api,
                'Content-Type: application/json',
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $datas = json_decode($response);

            return back()->with([
                'message' => 'Qualifier updated successfully. Thanks',
                'data' =>  $datas,
                'type' => 'bxs-check-circle'
            ]); 

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function endorse($request){
        $postData = array(
            'user' => $request->user,
            'school' => $request->school,
            'course_id' => $request->course_id,
            'type' => $request->type,
        );
        try{
            $url = $this->link.'/api/01101011%2001110010%2001100001%2001100100/qualifiers';
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->api,
                'Content-Type: application/json',
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $datas = json_decode($response);

            return back()->with([
                'message' => 'Endorsed successfully. Thanks',
                'data' =>  $datas,
                'type' => 'bxs-check-circle'
            ]); 

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function endorsed($request){
        $postData = array(
            'id' => $request->id,
            'user' => $request->user,
            'school_id' => $request->school_id,
            'course_id' => $request->course_id,
            'type' => $request->type,
        );
        try{
            $url = $this->link.'/api/01101011%2001110010%2001100001%2001100100/qualifiers';
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->api,
                'Content-Type: application/json',
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $datas = json_decode($response);

            return back()->with([
                'message' => 'Endorsed successfully. Thanks',
                'data' =>  $datas,
                'type' => 'bxs-check-circle'
            ]); 

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}
