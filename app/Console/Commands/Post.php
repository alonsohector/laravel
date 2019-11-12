<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

//Added to control POST, Timer and Attempts
use GuzzleHttp\Client;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Contracts\Cache\Repository as Cache;

class Post extends Command
{
    protected const COLOR_RED = 'COLOR_RED';
    protected const COLOR_GREEN = 'COLOR_GREEN';
    protected const COLOR_YELLOW = 'COLOR_YELLOW';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Incfile:POST {--U|URL_POST=Incfile_defined}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a simple POST request to external URL.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function logMessage($str, String $color = null)
    {
        switch ($color) {
            case self::COLOR_RED:
                $str = "\033[01;31m$str\033[0m";
                break;
            case self::COLOR_GREEN:
                $str = "\033[01;32m$str\033[0m";
                break;
            case self::COLOR_YELLOW:
                $str = "\033[01;33m$str\033[0m";
            break;
        }
        echo $str . PHP_EOL;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //echo PostController::index();
        $limiter = app(RateLimiter::class);

        echo "\n";
        $key = env('APP_KEY');

        $header = ["Attemps", "Retries Left", 'Available time (Sec)'];
        $info  = [
            [
                "attemps"=>(string) ($limiter->attempts($key)+1)
            ,
                "retries_left" => (string) ($limiter->retriesLeft($key, 3)<0)?0:$limiter->retriesLeft($key, env('POST_LIMITER_RETRIES'))    
            ,
                "available_time" => (string) ($limiter->availableIn($key)<0)?((int)env('POST_LIMITER_SECONDS')):($limiter->availableIn($key))
            ],
        ];
            
        $this->table($header, $info);

        //Review if there are not too manny attempts
        if ($limiter->tooManyAttempts($key, env('POST_LIMITER_RETRIES'))) {
            $this->logMessage("Too Many Attempts! pleaase wait after available time.", self::COLOR_RED );
        }else{
            // Initialize URL to be used for POST
            if($this->option('URL_POST')!="Incfile_defined"){
                $url = $this->option('URL_POST');     
            }else{
                $url = env('API_URL'); 
            }
            //echo the URL selected
            echo "URL: ".$url."\n";

            // Use get_headers() function 
            $headers = @get_headers($url); 
            // Use condition to check the existence of URL 
            ////retrieve status OK and created 
            if($headers && strpos( $headers[0], '200')) { 
                $status = true;//"URL Exist "; 
            } 
            else { 
                if($headers && strpos( $headers[0], '201')) { 
                    $status = true;//"URL Exist "; 
                } 
                else { 
                    $status = false;//"URL Doesn't Exist "; 
                } 
            } 
            //echo "estatus ".$status."\n";
            //if URL exist continue with POST, else throw error 
            if($status){
                //Start POST
                try {
                    /*
                    Init Client with 

                            Allow at most 10 redirects.
                            Use "strict" RFC compliant redirects.
                            Add a Referer header
                            Only allow https URLs                
                            Track redirects
                            connect_timeout for 2 seconds
                            close http_errors, handle errors
                    */
                    $client = new Client([
                        // Base URI is used with relative requests
                        'base_uri' => url($url),
                        //'base_uri' => 'http://localhost/post.php',
                        // You can set any number of default request options.
                        'timeout'  => 2.0,
                        'http_errors' => false,
                        'allow_redirects' => [
                            'max'             => 10,        // allow at most 10 redirects.
                            'strict'          => true,      // use "strict" RFC compliant redirects.
                            'referer'         => true,      // add a Referer header
                            'protocols'       => ['https'], // only allow https URLs
                            'track_redirects' => true
                        ],                    
                    ]);
                    
                    $response = $client->request('POST', '', [
                        'form_params' => [
                            'Parm1' => 'Parm1',             //Parameter get in command
                        ],
                        ['connect_timeout' => 2],
                        ['http_errors' => false]
                    ]  
                    );
                    
                    //retrieve status OK and created    
                    if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                        echo "\n*** POST sent correctly ***\n\n";

                        //charge attempt becaus POST was sent correctly
                        $limiter->hit($key, ((int)env('POST_LIMITER_SECONDS')));

                        if($this->output->isVerbose()){
                            echo response()->json(['POST' => "Completed"]);

                            if ($response->getBody()) {
                                echo "\nResponse:".$response->getBody();
                            } 
                        }
                    }
                
                } catch (\Exception $e) {
                    \Log::error("Incfile---l--".$e);
                    if(env('APP_ENV')!='production')
                        return back()->withError($e->getMessage())->withInput();
                }



            }else{

                //charge attempt becaus POST was sent bad or error
                $limiter->hit($key, ((int)env('POST_LIMITER_SECONDS')));

                //URL doest exist, throw error 
                $this->logMessage("Unable to connect. The url is not valid or you do not have permission to the site.", self::COLOR_RED );
                //Log Failure
                \Log::debug('Incfile - LOG: '.'URL does\'t exist.');
            }


        }//else - attempts        
    }
}
