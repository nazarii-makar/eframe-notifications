<?php

namespace EFrame\Notifications\Clients;

use Exception;
use GuzzleHttp\Client;
use EFrame\Notifications\Messages\SmsFlyMessage;

class SmsFlyClient
{
    const API_URL = 'http://sms-fly.com/api/api.php';

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $alfaname;

    /**
     * @var Client
     */
    protected $client;

    /**
     * SmsFlyClient constructor.
     *
     * @param      $login
     * @param      $password
     * @param null $alfaname
     */
    public function __construct($login, $password, $alfaname = null)
    {
        $this->login    = $login;
        $this->password = $password;
        $this->alfaname = $alfaname;

        $this->client = new Client(['auth' => [$this->login, $this->password]]);
    }

    /**
     * @param SmsFlyMessage $message
     * @param string|array  $recipients
     *
     * @return SmsFlyMessage
     * @throws Exception
     */
    public function send(SmsFlyMessage $message, $recipients)
    {
        $recipients = $this->processRecipients($recipients);

        $text = mb_convert_encoding(
            htmlspecialchars($message->content), 'utf-8'
        );

        $description = mb_convert_encoding(
            htmlspecialchars($message->description), 'utf-8'
        );

        foreach ($recipients as &$recipient) {
            $recipient = <<<EOT
<recipient>{$recipient}</recipient>
EOT;
        }

        $recipients = implode(PHP_EOL, $recipients);

        $data = <<<EOT
<message start_time="AUTO" end_time="AUTO" livetime="4" rate="1" desc="{$description}" source="{$this->alfaname}">
    <body>{$text}</body>
    {$recipients}
</message>
EOT;

        $this->makeOperation('SENDSMS', $data);

        return $message;
    }

    /**
     * @param        $operation
     * @param string $data
     *
     * @return mixed|\SimpleXMLElement
     */
    protected function makeOperation($operation, $data = '')
    {
        $xml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<request>
    <operation>{$operation}</operation>
    {$data}
</request>
EOT;

        $this->client->post(self::API_URL, [
            'headers' => [
                'Content-Type: text/xml',
                'Accept: text/xml',
            ],
            'body' => $xml
        ]);
    }

    /**
     * @param $recipients
     *
     * @return array
     */
    protected function processRecipients($recipients)
    {
        $recipients = (!is_array($recipients)) ? [$recipients] : $recipients;

        foreach ($recipients as &$recipient) {
            $recipient = str_replace('+', '', $recipient);
        }

        return $recipients;
    }

    /**
     * @return null|string
     */
    public function alfaname()
    {
        return $this->alfaname;
    }
}