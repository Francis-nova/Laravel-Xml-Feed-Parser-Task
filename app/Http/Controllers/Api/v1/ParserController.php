<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\XmlParseRequest;
use App\Http\Services\ParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Database\QueryException;

class ParserController extends Controller
{
    public function parseXml(Request $request, ParserService $parserService) {

        // check if request parameter is a string...
        $validator = $request->validate([
            'url' => 'required|string|url',
        ]);

        // you're good
        try{
            $json = $parserService->convertXmlToJson($request->url);
            return response()->json($json);
        } catch (\Exception $e){
            return response()->json([
                'error' => 'Failed to convert xml to json'
            ],500);
        }

    }
}


// Use laravel validator - done
// validator 422 response - done

// 500 error handling with App/Exception

