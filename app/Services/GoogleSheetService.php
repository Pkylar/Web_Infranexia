<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $sheetName;

    public function __construct()
    {
        $this->spreadsheetId = '1VHyfmKM-ZXNrYiDS06MGJyyqq3ySVT-DKZVy6SVLm2Y';
        $this->sheetName = 'DB WITEL';
        $this->client = new Client();

        $this->client->setApplicationName('Infralexia App');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('google/service-account.json'));
        $this->client->setAccessType('offline');

        $this->service = new Sheets($this->client);
    }

    public function readSheet()
    {
        $range = $this->sheetName;
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        return array_filter($values);
    }

    public function appendRow(array $values)
    {
        $range = $this->sheetName;
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [$values]
        ]);

        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }

}
