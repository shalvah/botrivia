<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetGetStartedButton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:getstarted:set {payload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the Get Started button';

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
        $data = [
            "get_started" => [
                "payload" => $this->argument("payload"),
            ]
        ];
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messenger_profile?access_token=' . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        $this->info($result);
    }
}
