<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use Response;

class SearchController extends Controller
{
    function index(){
        return view('autocomplete');
    }

    public function autocomplete(){
	$term = Input::get('term');
	
	$results = array();

	  if(@\Auth::user()->department!='Admissions'){
	$queries = DB::table('tpoly_students')
                ->where('INDEXNO','LIKE', '%'.$term.'%')
                ->orwhere('STNO', 'LIKE', '%'.$term.'%')
		->orwhere('SURNAME', 'LIKE', '%'.$term.'%')
		->orWhere('FIRSTNAME', 'LIKE', '%'.$term.'%')
                ->orWhere('OTHERNAMES', 'LIKE', '%'.$term.'%')
                ->orWhere('NAME', 'LIKE', '%'.$term.'%')
                
		->take(500)->get();
        foreach ($queries as $query)
	{
		if( $query->INDEXNO!=""){
			$results[] = [ 'id' => $query->ID, 'value' => $query->INDEXNO.','.$query->NAME ];
		}
		else{
	    $results[] = [ 'id' => $query->ID, 'value' => $query->STNO.','.$query->NAME ];
	}
	}
return Response::json($results);
        }
        else{
            $queries = DB::table('tpoly_applicants')
                ->where('APPLICATION_NUMBER','LIKE', '%'.$term.'%')
                 
		->orwhere('SURNAME', 'LIKE', '%'.$term.'%')
		->orWhere('FIRSTNAME', 'LIKE', '%'.$term.'%')
                ->orWhere('OTHERNAME', 'LIKE', '%'.$term.'%')
                ->orWhere('NAME', 'LIKE', '%'.$term.'%')
                
		->take(500)->get();
            foreach ($queries as $query)
	{
		if( $query->APPLICATION_NUMBER!=""){
			$results[] = [ 'id' => $query->ID, 'value' => $query->APPLICATION_NUMBER.','.$query->NAME ];
		}
		else{
	   	}
	}
return Response::json($results);
        }
	
}
 
} 