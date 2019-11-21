<?php

namespace ScoutEngines\Elasticsearch;

use Illuminate\Console\Command;

class EsInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:init {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'elasticsearch create mapping';

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
        $class = $this->argument('model');

        $model = new $class;

        $client = new \GuzzleHttp\Client();
        $url = config('scout.elasticsearch.hosts')[0] . '/' . $model->getTable().'/_mapping';

//        $client->delete(config('scout.elasticsearch.hosts')[0] . '/' . $model->getTable());
        $client->put(config('scout.elasticsearch.hosts')[0] . '/' . $model->getTable());

        $data = [];
        foreach ($model->toSearchableArray() as $key => $value){
            $data[$key] =  [
                "type" =>  "text",
                "analyzer" =>  "ik_max_word",
                "search_analyzer"=>  "ik_max_word"

            ];
        }

        $client->post($url, [
            \GuzzleHttp\RequestOptions::JSON => [
                "properties" => $data
            ]
        ]);
    }
}
