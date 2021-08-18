<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Exception;
use Storage;
use DOMDocument;
use Symfony\Component\Console\Input\InputArgument;

class CsvtoJsonXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:csv-json-xml {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Csv file to json and xml';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

  
    protected function getArguments()
    {
        return [['path', InputArgument::REQUIRED, "File path"]];
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('path');
        if(!@file_get_contents($file)) {
            echo "File not found!";
            exit;           
        }
        echo "Converting Start At " . date('Y-m-d h:i:s A');
        //Convert to jsonilele
        $json = $this->csvToJson($file);
        if($json) {
            echo " => Converted to json Completed At " . date('Y-m-d h:i:s A');
        }
        
        //Convert to xml
        $xml = $this->csvToXml($file);
        if($xml) {
            echo " => Converted to xml Completed At " . date('Y-m-d h:i:s A');
        }
    }

    /**
     * Csv to json function
     *
     * @return boolen
     */
    protected function csvToJson($file)
    {
        try{
            $csv = file($file);
        
            $csvData = array();

            $columns = fgetcsv(fopen($file,"r"));

            foreach ($csv as $index => $row) {
                if ($index > 0) { // Assume the the first row contains the column names.
                    $newRow = [];
                    $values = explode(',', $row);
                    foreach ($values as $colIndex => $value) {
                        $newRow[$columns[$colIndex]] = trim($value);
                    }
                    $csvData[] = $newRow;
                }
            }
            
            $json =  json_encode($csvData);
            $this->generateFile('result.json',$json);
            
            return true;
        } 
        catch(Exception $ex) {
            return false;
        }
    }
    /**
     * Csv to xml function
     *
     * @return boolen
     */
    protected function csvToXml($file) 
    {
        try{
            $inputFile = fopen($file,"r");

            $headers = fgetcsv($inputFile);

            $doc  = new DomDocument();
            $doc->formatOutput = true;

            $root = $doc->createElement('products');
            $root = $doc->appendChild($root);

            while (($row = fgetcsv($inputFile)) !== FALSE)
            {
                $container = $doc->createElement('product');
                foreach($headers as $i => $header)
                {
                    $child = $doc->createElement($header);
                    $child = $container->appendChild($child);
                    $value = $doc->createTextNode(trim($row[$i]));
                    $value = $child->appendChild($value);
                }

                $root->appendChild($container);
            }

            $xml = $doc->saveXML();

            $this->generateFile('result.xml', $xml);
            return true;
        } 
        catch(Exception $ex) {
            return false;
        }
    }

    protected function generateFile($fileName,$format) {
        $handle = fopen($fileName, 'w');
        fwrite($handle, $format);
        fclose($handle);
    
        Storage::disk('local')->put('public/'.$fileName, $format);
    }

    
}
