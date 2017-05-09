<?php

namespace App\Jobs;

use App\Bot\Bot;
use App\Bot\Trivia;
use App\Bot\Webhook\Messaging;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class BotHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messaging;

    /**
     * Create a new job instance.
     *
     * @param Messaging $messaging
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        $bot = new Bot($this->messaging);
        $custom = $bot->extractData();

        //a request for a new question
        if ($custom["type"] == Trivia::$NEW_QUESTION) {
            $bot->reply(Trivia::getNew());
        } else if ($custom["type"] == Trivia::$ANSWER) {
            if (Cache::has("solution")) {
                $bot->reply(Trivia::checkAnswer($custom["data"]["answer"]));
            } else {
                $bot->reply("Looks like that question has already been answered. Try \"new\" for a new question");
            }
        } else if ($custom["type"] == "get-started") {
            $bot->sendWelcomeMessage();
            $bot->reply(Trivia::getNew());
        } else {
            $bot->reply("I don't understand. Try \"new\" for a new question");
        }
    }
}
