@extends('layouts.printlayout')

@section('content')

 
      
                    @inject('sys', 'App\Http\Controllers\SystemController')
                 <table style="margin-top: -50"   class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
             
               <thead>
                 <tr>
                    <th></th> 
                    <th></th> 
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>                       
                </tr>
             </thead>
             <tbody>
                              <?php
                            $kocount = -1;
                             ?>
                             <tr align=""  >           
                             @foreach($mark as $index=> $row) 
                            

                              <?php
                            $kocount = $kocount + 1;
                            if ($kocount == 2) {
                            $kocount = 0;
                            ?>
                            </tr>
                            <tr align="">
                            <?php
                            } 
                             ?>
                            
                                <td><img class=" " style="width:130px;height: auto" src="{!!url('public/albums/students/'.$row->INDEXNO.'.JPG') !!}" alt=" Picture of Student Here"    /></td>

                                <td style="border-right: dotted;" width="50"> {{ @$row->INDEXNO }} , LEVEL {{$row->LEVEL}}<br/>{{ @$row->NAME }}<br/>{!! strtoupper($sys->getProgramName($row->PROGRAMMECODE)) !!}<br/> {!! QrCode::size(100)->generate(Request::url()); !!}

                                </td>
                                                                
                              @endforeach
                            </tr>
                             
                        </tbody>
                                    
                             </table>
           
    
 
 
        
 @endsection
 
@section('js')
 <script type="text/javascript">
  
$(document).ready(function(){
window.print();
//window.close();
});

</script>
  
@endsection