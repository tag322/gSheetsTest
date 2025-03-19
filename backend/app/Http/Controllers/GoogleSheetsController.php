<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\GoogleSheet;

class GoogleSheetsController extends Controller
{
    function fetchSheet(Request $request, $sheet_id) {

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token
        ])
        ->get(env('GOOGLE_SCRIPTS_APP_URL') , [
            'limit' => $request->limit,
            'page' => $request->page 
        ]);

        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $request->access_token
        // ])
        // ->get('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . '/values/Лист1!A1:Z' , [
        //     'key' => env('GOOGLE_API_KEY'),
        //     'valueRenderOption' => 'FORMATTED_VALUE',
        //     'majorDimension' => 'ROWS'
        // ]);

        return $response;
    }

    function updateSheet(Request $request, $sheet_id) {

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token,
            'Accept' => 'application/json',
            
        ])
        
        ->post(env('GOOGLE_SCRIPTS_APP_URL'), [
            'values'=> $request->table
        ]);


        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $request->access_token,
        //     'Accept' => 'application/json',
        // ])
        // ->post('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . '/values/Лист1!A1:Z:append' . '?key=' . env('GOOGLE_API_KEY') . '&valueInputOption=RAW', [
        //     'values'=> [$request->table]
        // ]);

        return $response;
    }

    function deleteRow(Request $request, $sheet_id) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
        ->withBody('{}', 'application/json')
        ->post('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . "/values/A{$request->indexToRemove}:ZZ{$request->indexToRemove}:clear" . '?key=' . env('GOOGLE_API_KEY'));

        return response($response->body(), $response->status())
        ->withHeaders($response->headers());
    }

    function editRow(Request $request, $sheet_id) {

        $row = $request->row;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
        ->post('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . ":batchUpdate" . '?key=' . env('GOOGLE_API_KEY'), [
            'requests' => [
                'updateCells' => [
                    "range" => [
                        "sheetId" => 0,
                        "startRowIndex" => end($row),
                        "endRowIndex" => end($row)+1,
                        "startColumnIndex" => 0,
                        "endColumnIndex" => count($row)
                    ],
                    'rows' => [
                        'values' => [
                            [
                                'userEnteredValue' => [
                                    'numberValue' => $row[0]
                                ]
                            ],
                            [
                                'userEnteredValue' => [
                                    'stringValue' => $row[1]
                                ]
                            ],
                            [
                                'userEnteredValue' => [
                                    'stringValue' => $row[2]
                                ]
                            ]
                        ]
                    ],
                    'fields' => "userEnteredValue"
                ]
            ]
        ]);

        return response($response->body(), $response->status())
        ->withHeaders($response->headers());
    }


    function eraseTable(Request $request, $sheet_id) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
        ->withBody('{}', 'application/json')
        ->post('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . "/values/A2   :ZZ{$request->table_rows_count}:clear" . '?key=' . env('GOOGLE_API_KEY'));

        return response($response->body(), $response->status())
        ->withHeaders($response->headers());
    }

    function fillGoogleTable(Request $request, $sheet_id) {
        $data = GoogleSheet::all('id', 'status', 'commentary')->toArray();

        $data = array_map(function($item) {
            return array_values($item);
        }, $data);
    
        Log::info('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . '/values' . '/A2:Z' . count($data) . '?valueInputOption=USER_ENTERED');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $request->access_token,
            'Accept' => 'application/json',
            
        ])
        ->put('https://sheets.googleapis.com/v4/spreadsheets/' . $sheet_id . '/values' . '/A2:Z' . (count($data)+1) . '?valueInputOption=USER_ENTERED', [
            'values'=> $data
        ]);

        return response($response->body(), $response->status())
        ->withHeaders($response->headers());
    }
}
