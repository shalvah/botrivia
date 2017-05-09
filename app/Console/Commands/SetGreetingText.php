<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetGreetingText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:greeting:set {text} {--locale=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the greeting text for our bot';

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
            "greeting" => [
                [
                    "locale" => $this->option("locale"),
                    "text" => $this->argument("text")
                ]
            ]
        ];
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messenger_profile?access_token=' . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $this->info(curl_exec($ch));
    }
}
