<?php
 
namespace App\Http\Controllers;
use Gate;
use Illuminate\Http\Request;
use App\Models;
use App\User;
use App\Models\AcademicRecordsModel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class BackupController extends Controller
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
    public function backup(){
        $manager->makeBackup()->run('development', 'dropbox', 'test/backup.sql', 'gzip');
    }
    public function restore() {
        $manager->makeRestore()->run('s3', 'test/backup.sql.gz', 'development', 'gzip');
    }
}