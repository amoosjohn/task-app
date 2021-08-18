<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use DomDocument;
use Exception;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
            $input = $request->only(['name','pvp']);
            $file = storage_path('app\public\result.json');
            $content = File::get($file);
            $decode = json_decode($content);
            
            $data = collect($decode);
            if($input['name'] || $input['pvp']) {
                $data = $data->filter(function ($item) use ($input) {

                    return ($input['name'] && stristr($item->name, $input['name']) !== false) || ($input['pvp'] && stristr($item->pvp, $input['pvp']) !== false);
                
                })->values();
            }
            $data = $data->toArray();
            $xml = $this->arrayToXml($data);

            return response($xml, 200, ['Content-Type' => 'application/xml']);
       
       
            return response()->json(['message'=>'Something went wrong!']);
        
        
        
    }

  
    /**
     * Array to xml function
     *
     * @return $xml
     */
    protected function arrayToXml($data) {
        $doc  = new DomDocument();
        $doc->formatOutput = true;

        $root = $doc->createElement('products');
        $root = $doc->appendChild($root);
        
        $headers = [];
        foreach($data as  $row)
        {
            $container = $doc->createElement('product');
            $row = (array)$row;
            if(count($headers)  == 0) {
                $headers = array_keys($row);
            }
            
            foreach($headers as $i => $header)
            {
                $child = $doc->createElement($header);
                $child = $container->appendChild($child);
                $value = $doc->createTextNode($row[$header]);
                $value = $child->appendChild($value);
            }
            $root->appendChild($container);
        }

        $xml = $doc->saveXML();
        return $xml;
    }

}
