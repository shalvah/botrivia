<?php

namespace App\Bot;


use App\Bot\Webhook\Messaging;
use Illuminate\Support\Facades\Log;

class Bot
{
    private $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function extractData()
    {
        $type = $this->messaging->getType();
        if ($type == "message") {
            return $this->extractDataFromMessage();
        } else if ($type == "postback") {
            return $this->extractDataFromPostback();
        }
        return [];
    }

    public function extractDataFromMessage()
    {
        $matches = [];

        $qr = $this->messaging->getMessage()->getQuickReply();
        if (!empty($qr)) {
            $text = $qr["payload"];
        } else {
            $text = $this->messaging->getMessage()->getText();
        }
        //single letter message means an answer
        if (preg_match("/^(\\w)\$/i", $text, $matches)) {
            return [
                "type" => Trivia::$ANSWER,
                "data" => [
                    "answer" => $matches[0]
                ]
            ];
        } else if (preg_match("/^(new|next)(\s*question)?\$/i", $text, $matches)) {
            return [
                "type" => Trivia::$NEW_QUESTION,
                "data" => []
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function extractDataFromPostback()
    {
        $payload = $this->messaging->getPostback()->getPayload();

        if (preg_match("/^(\\w)\$/i", $payload)) {
            return [
                "type" => Trivia::$ANSWER,
                "data" => [
                    "answer" => $payload
                ]
            ];
        } else if ($payload === "get-started") {
            return [
                "type" => "get-started",
                "data" => []
            ];
        }
        return [
            "type" => "unknown",
            "data" => []
        ];
    }

    public function sendWelcomeMessage()
    {
        $name = $this->getUserDetails()["first_name"];
        $this->reply("Hi there, $name! Welcome to botrivia! You can type \"new\" to get a new question, but why don’t we start with this one?");
    }

    private function getUserDetails()
    {
        $id = $this->messaging->getSenderId();
        $ch = curl_init("https://graph.facebook.com/v2.6/$id?access_token=" . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        return json_decode(curl_exec($ch), true);
    }

    public function reply($data)
    {
        if (method_exists($data, "toMessage")) {
            $data = $data->toMessage();
        } else if (is_string($data)) {
            $data = ["text" => $data];
        }
        $id = $this->messaging->getSenderId();
        $this->sendMessage($id, $data);
    }

    private function sendMessage($recipientId, $message)
    {
        $messageData = [
            "recipient" => [
                "id" => $recipientId
            ],
            "message" => $message
        ];
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        Log::info(print_r(curl_exec($ch), true));
    }
}