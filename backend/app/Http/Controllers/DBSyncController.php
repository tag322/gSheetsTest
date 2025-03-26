<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoogleSheet;

use Illuminate\Support\Facades\Log;

class DBSyncController extends Controller
{
    function import(Request $req) {
        $dataToInsert = [];

        foreach ($req->data as $row) {
            array_push($dataToInsert, [
                'id' => $row[0],
                'status' => $row[1],
                'commentary' => $row[2]
            ]);
        }

        array_shift($dataToInsert);
        Log::info($dataToInsert);

        GoogleSheet::insert($dataToInsert);

        return response('success');
    }

    function erase() {
        GoogleSheet::query()->delete();

        return response('success')
        ->header('Content-Type', 'application/json');
    }

    function seed() {
        GoogleSheet::factory()->count(1000)->create();

        return response('success')
        ->header('Content-Type', 'application/json');
    }
}
