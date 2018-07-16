<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankModel;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
//use Input;

use App\Item;

use DB;

use Excel;

class MaatwebsiteDemoController extends Controller

{

	public function importExport()

	{

		return view('importExport');

	}

	public function downloadExcel($type)

	{

		$data = Item::select('title','description')->get()->toArray();

		return Excel::create('itsolutionstuff_example', function($excel) use ($data) {

			$excel->sheet('mySheet', function($sheet) use ($data)

	        {

				$sheet->fromArray($data);

	        });

		})->download($type);

	}

	public function importExcel(Request $request)

	{

		if($request->hasFile('import_file')){

			$path = $request->file('import_file')->getRealPath();

			$data = Excel::load($path, function($reader) {

			})->get();

			if(!empty($data) && $data->count()){

				foreach ($data as $key => $value) {

					$insert[] = ['title' => $value->title, 'description' => $value->description];

				}

				if(!empty($insert)){

					DB::table('items')->insert($insert);

					dd('Insert Record successfully.');

				}

			}

		}

		return back();

	}

}