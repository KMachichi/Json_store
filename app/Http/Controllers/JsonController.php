<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class JsonController extends Controller
{

    /**
     * Store files content to database
     */
    public function store(Request $request)
    {
    	$extension = $request->extension;

    	if (!in_array($extension, ['json',/*'csv','xml'*/] )) {
    		return $this->getResponse('Invalid extension !');
    	}

        // If we want to store other extensions files as xml or csv 
        $jsonFiles  = glob(storage_path('app/public/*.'.$extension));

        if (!count($jsonFiles)) {
            return $this->getResponse('No files to store');
        }

        // case json files more than one
        foreach($jsonFiles as $jsonFilePath) {
            // Check size if is higher than can in parts 
            $size = round(filesize($jsonFilePath) / 1024 / 1024, 1).' MB';

            $fileName = pathinfo($jsonFilePath, PATHINFO_FILENAME);
            $extension = pathinfo($jsonFilePath, PATHINFO_EXTENSION);

            $data =  json_decode(file_get_contents($jsonFilePath),true);
            $partsNumber = preg_match('/_/',pathinfo($fileName, PATHINFO_FILENAME)) ? (int) pathinfo($fileName, PATHINFO_FILENAME) : 0;

            $importFiles = [
                            'name' => $fileName,
                            'parts_number' => $partsNumber,
                            'extension' => $extension,
                            'imported'=> 0,
                            'start_time'=> date("H:i:s"),
                            ];

            $fileId = '';

            try {
                $fileId = DB::table('imported_files')->insertGetId($importFiles);
            } catch (Exception $e) {
                Log::critical($e->getMessage());
            }

            if (!$fileId) {
                return $this->getResponse('Somthing goes wrong Check you\'r error log');
            }

            foreach($data as $key => $value) {
                $date = str_replace('/','-',$value['date_of_birth']);
                $year =  Carbon::createFromDate($date)->diff(Carbon::now())->format('%y');
                if ($year > 18 && $year < 65 || !$date) {
                    $creditCard = $value['credit_card'];
                    unset($value['credit_card']);
                    $value['date_of_birth'] = $date ? Carbon::parse($date)->format('Y-m-d') : null; 
                    $value['file_id'] = $fileId;
                    $value['key'] = $key;

                    $clientId = '';
                    try {
                        $clientId = DB::table('clients')->insertGetId($value);
                    } catch(Exception $e) {
                        Log::critical($e->getMessage());
                    }

                    if (!$clientId) {
                        return $this->getResponse('Somthing goes wrong Check you\'r error log');                    
                    }

                    $creditCard['client_id'] = $clientId;
                    $creditCard['expiration_date'] = Carbon::parse($creditCard['expirationDate'])->format('Y-m-d');
                    unset($creditCard['expirationDate']);
                    
                    $creditCardId = '';
                    try {  
                        $creditCardId = DB::table('credit_cards')->insertGetId($creditCard);
                    } catch(Exception $e) {
                        Log::critical($e->getMessage());
                    }

                    if (!$clientId) {
                        return $this->getResponse('Somthing goes wrong Check you\'r error log');                  
                    }    
                }
            }

            DB::table('imported_files')->where('id',$fileId)->update(['imported' => true,'end_time' => date("H:i:s")]);

            return $this->getResponse('End store data');
            die;
        }
    }

    /**
     * Return response error
     */
    
    public function getResponse($error)
    {
        return response()->json(['error' => $error]);
    }
}
