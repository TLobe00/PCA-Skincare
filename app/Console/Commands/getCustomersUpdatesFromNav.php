<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\customers;
use GuzzleHttp;
use App\Jobs\PushCustomersToShopify;

class getCustomersUpdatesFromNav extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pca:getCustomersUpdatesFromNav';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //CustomerCardPage?$format=json

        $client = new GuzzleHttp\Client(['auth' => [env('HTTP_USERNAME', 'usuari'), env('HTTP_PASSWORD', '')]]);
        $uri=env('HTTP_URIBASE', '');
        $port=env('HTTP_URIPORT', '');
        $server=env('HTTP_URINAVSERVER', '');
        $service=env('HTTP_URINAVSERVICE', '');
        $company=env('HTTP_URINAVCOMPANY', '');
        
        $credentials = base64_encode(env('HTTP_USERNAME', 'usuari').':'.env('HTTP_PASSWORD', ''));

        $customers = customers::whereNotNull('shopify_id');
//        if( $this->option('customer') )
            $customers->where( 'nav_no', '=', '101586' );
        $customers = $customers->orderBy('updated_at', 'ASC')->get();  //ASC  ->limit(100)  $customers->orderBy('updated_at', 'DESC')->get();
        foreach ($customers as $customer) {

            try {
                $apiRequest = $client->request('GET', $uri.':'.$port.'/'.$server.'/'.$service.'/Company(\''.$company.'\')/CustomerCardPage(\''.$customer->nav_no.'\')?$format=json',[
                        'auth' => [env('HTTP_USERNAME', 'usuari'),env('HTTP_PASSWORD', ''), 'ntlm' ]     
                        ]);
                $content = json_decode($apiRequest->getBody()->getContents());
                
                //dd($content);

                if(strtotime($content->Last_Date_Modified) > strtotime($customer->updated_at)) {
                    //dd('greater than');

//                    foreach ($content as $key => $value) {
                        # code...
                        //print $key . " - ";
//                        dd($content);
//                        if ( $key == 'value' ) {
                            //print_r($value);
//                            foreach ($value as $key2 => $value2) {
                                # code...
                                //print $value2->ETag . " - ";

                                //$customer = new \App\customers;
                                //$customer->name = $request->name;

                                //$customer->nav_no = $value2->No;
                                $customer->name = $content->Name;
                                $customer->address = $content->Address;
                                $customer->address_2 = $content->Address_2;
                                $customer->city = $content->City;
                                $customer->county = $content->County;
                                $customer->post_code = $content->Post_Code;
                                $customer->country_region_code = $content->Country_Region_Code;
                                $customer->phone_no = $content->Phone_No;
                                $customer->tax_liable = $content->Tax_Liable;
                                $customer->e_mail = $content->E_Mail;
                                $customer->memo = $content->Memo;
                                $customer->memo_2 = $content->Memo_2;
                                //$customer->etag = $value2->ETag;

                                
                                //dd($customer);
                                $customer->save();
                                
                                //print "Save customer " . $customer->name . "\n";
                                //PushCustomersToShopify::dispatch()->delay(now());
                                dispatch(new PushCustomersToShopify($customer->shopify_id));
//                            }
//                        }
//                    }

                } else {
                    dd('less than');
                }

            //$etag= $content->ETag;
            } catch (GuzzleHttp\Exception\ClientException $exception) {
                $responseBody = $exception->getResponse()->getBody(true);
                print $responseBody;
            }

        }

    }
}
