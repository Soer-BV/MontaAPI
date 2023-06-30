<?php

namespace SoerBV\Api;

/**
 * @author Rick de Boer <r.deboer.soer.nl>
 *  Permission is hereby granted, free of charge,
 * to any person obtaining a copy of this software
 * and associated documentation files (the "Software"),
 * to deal in the Software without restriction according to the MIT license.
 */

class Client
{
    protected string $url = '';
    protected string $username = '';
    protected string $password = '';

    public function __construct(string $username, string $password, string $url = 'https://api.montapacking.nl/rest/v5')
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws Exception
     */
    public function sendRequest($endpoint, $method, $params = [], $data = null)
    {
        $curl = curl_init();
        $url = $this->url . '/' . $endpoint . '?' . http_build_query($params);

        switch ($method) {
            case "GET":
                curl_setopt($curl, CURLOPT_HTTPGET, 1);
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array (
           'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)
        ));

        $result = curl_exec($curl);
        $headerInfo = curl_getinfo($curl);
        $acceptedHeaders = [200, 201, 204];

        if ($headerInfo['http_code'] != in_array($headerInfo['http_code'], $acceptedHeaders)) {
            throw new Exception('Status ' . $headerInfo['http_code'] . ' received: ' . $result);
        }

        curl_close($curl);
        return $result;
    }

    /*
     * Fetch latest events in Monta
     * You can filter the latest events using the latest known event ID that you have synchronised.
     * Maximum: 200 events events per request
     */
    /**
     * @throws Exception
     */
    public function getEvents(int $id)
    {
        return $this->sendRequest('orderevents/' . $id, 'GET');
    }
}


