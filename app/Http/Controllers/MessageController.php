<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use Storage;

class MessageController extends Controller
{
    public function form(){
        return view('form');
    }

    protected function validator(array $data){
        return Validator::make($data, [
            "senderid" => ["required", "string", "max:255"],
            "recipient" => ["required", "string"],
            "message" => ["required", "string"],
            "page_count" => ["required", "integer"],
        ]);
    }

    public function send(Request $request){
        //Get Form Request
        $input = $request->all();

        //Validating Request Entry
        $validator = $this->validator($input);
        if($validator->fails()){
            return json_encode([
                "message" => "Invalid file entry",
                "error" => $validator->errors()
            ]);
        }

        $numbers = $input['recipient'];
        preg_match_all('/\b\d{10,13}\b/', $numbers ,$matches);
        $numbers = join(", ", $matches[0]);
        $numbers = " {$numbers}";

        $numbers = preg_replace('/(,\s*|\s+)(0)(70|80|90|81)(\d+)/', ' 234$3$4', $numbers);

        $numbers = " {$numbers}";
        $total_price = 0;
        $total_numbers = 0;


        $handle = @fopen(storage_path() . "/app/public/priceList.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(strlen($line) < 3) continue;
                $item = explode("=", $line);
                $search_string = "/(,\\s*|\\s+)({$item[0]})\\d+/";
                $matches = null;
                preg_match_all($search_string, $numbers, $matches);
                $price = count($matches[0]) * (float) $item[1];
                if($price > 0){
                    $total_numbers += count($matches[0]);
                    $total_price += $price * $input['page_count'];
                }
            }

            fclose($handle);

            $finalData['senderid'] = $input['senderid'];
            $finalData['total_numbers'] = $total_numbers;
            $finalData['total'] = $total_price;
            $finalData['page_count'] = $input['page_count'];
            $finalData['message'] = $input['message'];
            return json_encode([
                'items' => $finalData
            ]);
        } else {
            return json_encode([
                "message" => "A server error occurred",
                "error" => [
                    'file' => ["a file is missing"]
                ]
            ]);
        }
    }
}
