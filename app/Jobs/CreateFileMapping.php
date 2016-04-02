<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Cache;
use App\File;
use App\Token;
use App\AppModels\Provider;
use Auth;
use DB;
use PDOException;

class CreateFileMapping extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $connObj;
    private $connName;
    private $root;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conName)
    {
        $this->connName = $conName;
        $this->connObj = new Provider($conName);
        $query = File::roots()->where('token_id', $this->connObj->getTokenId())->first();
        if ($query !== null){
            $this->root = $query;
            $this->root->delete();
        }
        $this->root = File::create([
            'name' => 'root',
            'path' => 'root',
            'token_id' => $this->connObj->getTokenId()
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        printf("Start Working... : %s \n", $now);
        $f = $this->processData();
        printf("Finish Processing...: %u seconds\n", $now->diffInSeconds());
        $this->root->makeTree($f);
        printf("Successfully Saved! : %s \nTotal Time: %u seconds\n", $this->connName, $now->diffInSeconds());

        
    }

    private function processData($path = '/')
    {
        $files = array();
        $rec_data = $this->connObj->getFiles($path);
        foreach ($rec_data as $d) {
//            unset($d['provider']);
            $d += [
                'token_id' => $this->connObj->getTokenId(),
                'updated_at' => Carbon::now()
            ];
            if (!$d['is_dir']){
                array_push($files, $d);
            }else{
//                $d['is_dir'] = $this->processData($d['path']);
                $d['children'] = $this->processData($d['path']);
                array_push($files, $d);
            }
        }
        return $files;

    }


}
