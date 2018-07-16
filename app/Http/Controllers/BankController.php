<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankModel;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class BankController extends Controller
{
     
    /**
     * Create a new controller instance.
     *
     
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
       
         
    }
     public function log_query() {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }
    public function new_receiptno(){
        $receiptno_query = Models\Receiptno::first();
		$receiptno_query->increment("receiptno", 1);
        $receiptno = str_pad($receiptno_query->receiptno, 12, "0", STR_PAD_LEFT);
		
        return $receiptno;
        
    }
    
    public function pad_receiptno($receiptno){
       return str_pad($receiptno, 12, "0", STR_PAD_LEFT);
       }
    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getIndex(Request $request)
    {
        
        return view('banks.index');
    }
    public function anyData(Request $request)
    {
         
        $banks = BankModel::select([  'ID','NAME', 'ACCOUNT_NUMBER']);


        return Datatables::of($banks)
              
             ->addColumn('action', function ($bank) {
                 return "<a href=\"edit_bank/$bank->ID/id\" class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light waves-effect waves-button waves-light\"><i title='click to edit' class=\"sidebar-menu-icon material-icons md-18\">edit</a>";
            
                //return' <td> <a href=" "><img class="" style="width:70px;height: auto" src="public/Albums/students/'.$student->INDEXNO.'.JPG" alt=" Picture of Employee Here"    /></a>df</td>';
                          
                                         
            })
            ->setRowId('id')
            ->setRowClass(function ($bank) {
                return $bank->ID % 2 == 0 ? 'uk-text-success' : 'uk-text-warning';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])
                  
            ->make(true);
             
            //flash the request so it can still be available to the view or search form and the search parameters shown on the form 
      //$request->flash();
    }

     public function form()
    { 
        return view('banks.create');
             
        
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'account'=>'required'
        ]);

      
      $total=count($request->input('bank'));
      $name=$request->input('bank');
      $account=$request->input('account');
       
      for($i=0;$i<$total;$i++){
          $bank=new BankModel();
          $bank->NAME=$name[$i];
           $bank->ACCOUNT_NUMBER=$account[$i];
           $bank->save();
          
      }
       if(!$bank){
      
          return redirect("/banks")->withErrors("Following banks N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> bank could not be added </span>could not be added!");
          }else{
           return redirect("/banks")->with("success","Following banks:<span style='font-weight:bold;font-size:13px;'> bank added </span>successfully added! ");
              
              
          }
       
          
       
    }
    // show form for edit resource
    public function edit($id){
        $bank = BankModel::where("ID", $id)->firstOrFail();
        return view('banks.edit')->with('bank', $bank);
    }

    public function update(Request $request, $id){
         $query=  BankModel::where("ID",$id)->update(array("NAME"=>$request->input('bank'),"ACCOUNT_NUMBER"=>$request->input('account')));
         $banks=$request->input('bank');
         if(!$query){
      
          return redirect("/banks")->withErrors("Following banks N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> $banks </span>could not be updated!");
          }else{
           return redirect("/banks")->with("success","Following banks:<span style='font-weight:bold;font-size:13px;'> $banks</span>successfully updated! ");
              
              
          }
    }
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }
}
