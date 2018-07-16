$querySize=  Models\StudentModel::where("PROGRAMMECODE",$program)
                  ->where("LEVEL",$level)->where("STATUS",'In SCHOOL')->where("CLASS_GROUPS","0")->orderBy("INDEXNO")->count("*");
       $classSize=  Models\StudentModel::where("PROGRAMMECODE",$program)
                  ->where("LEVEL",$level)->where("STATUS",'In SCHOOL')->where("CLASS_GROUPS","0")->orderBy("INDEXNO")->get()->toArray();
       dd(count($classSize));
       $perGroup=  array_slice($classSize, $total);
           dd(  $perGroup);
        foreach($perGroup as $groups=>$group){
//          $query=  Models\StudentModel::where("PROGRAMMECODE",$program)
//                  ->where("LEVEL",$level)->limit($total)->orderBy("INDEXNO")->get();
            Models\StudentModel::where("INDEXNO",$group["INDEXNO"])->update(
                    array("CLASS_GROUPS"=>$name)
                    );
        }