<?php

namespace App\Bot\Webhook;

use Illuminate\Http\Request;

class Entry
{
    private $time;
    private $id;
    private $messagings;

    private function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->time = $data["time"];
        $this->messagings = [];
        foreach ($data["messaging"] as $datum) {
            $this->messagings[] = new Messaging($datum);
        }
    }

    public static function getEntries(Request $request)
    {
        $entries = [];
        $data = $request->input("entry");
        foreach ($data as $datum) {
            $entries[] = new Entry($datum);
        }
        return $entries;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMessagings()
    {
        return $this->messagings;
    }
}