<?php

namespace SoerBV\Monta;

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

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    public function __construct(string $username, string $password, string $url = 'https://api.montapacking.nl/rest/v5')
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws Exception
     */
    public function sendRequest($endpoint, $params = [], $method = self::METHOD_GET, $data = null)
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
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

    /************************
    * Health Endpoint
    ************************/

    /**
     * Get health of the Monta API
     * @return bool|string
     * @throws Exception
     */
    public function getHealth()
    {
        return $this->sendRequest('health/');
    }

    /*************************
     * Product Endpoint
     ************************/

    /**
     * Retrieve a single product by SKU
     * @param $sku
     * @return bool|string
     * @throws Exception
     */
    public function getProduct($sku)
    {
        return $this->sendRequest('product/' . $sku);
    }

    /**
     * Retrieve a single product by barcode
     * @param $barcode
     * @return bool|string
     * @throws Exception
     */
    public function getProductByBarcode($barcode)
    {
        return $this->sendRequest('product?barcode=' . $barcode);
    }

    /**
     * Retrieve a list of products by page number
     * @param $page
     * @return bool|string
     * @throws Exception
     */
    public function getProductByPage($page)
    {
        return $this->sendRequest('products?page=' . $page);
    }

    /**
     * Retrieve product stock details
     * @param $sku
     * @param bool $includeSplitStock
     * @return bool|string
     * @throws Exception
     * @parm $includeSplitStock
     */
    public function getProductStock($sku, bool $includeSplitStock = false)
    {
        $params =
            [
                'sku' => $sku,
                'includeSplitStock' => $includeSplitStock ? 'true' : 'false',
            ];

        return $this->sendRequest('products/stock', $params);

    }

    /**
     * Retrieve products with a changed stock since the provided date
     * @param $date
     * @return bool|string
     * @throws Exception
     */
    public function getUpdatedProducts($date)
    {
        return $this->sendRequest('product/updated_since/' . $date . '?&stock=1&stock=2&stock=3&stock=4&stock=5&stock=6&stock=7&stock=8&stock=9&stock=10');
    }

    /**
     * Create a product
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createProduct($data)
    {
        return $this->sendRequest('product', [], 'POST', $data);
    }

    /**
     * Change stock of a product with a stock mutation
     * @param $sku
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createStockMutation($sku, $data)
    {
        return $this->sendRequest('product/' . $sku . '/stockmutations', [], self::METHOD_POST, $data);
    }

    /**
     * Update details of a product
     * @param $sku
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function updateProduct($sku, $data)
    {
        return $this->sendRequest('product/' . $sku, [], self::METHOD_GET, $data);
    }

    /**
     * Delete a single barcode from a product
     * @param $sku
     * @param $barcode
     * @return bool|string
     * @throws Exception
     */
    public function deleteSingleBarcode($sku, $barcode)
    {
        return $this->sendRequest('product/' . $sku . '/barcode/' . $barcode, [], self::METHOD_DELETE);
    }

    /**
     * Delete all barcodes from a product
     * @param $sku
     * @return bool|string
     * @throws Exception
     */
    public function deleteAllBarcodes($sku)
    {
        return $this->sendRequest('product/' . $sku . '/barcode/', [], self::METHOD_DELETE);
    }

    /**
     * Delete a product
     * @param $sku
     * @return bool|string
     * @throws Exception
     */
    public function deleteProduct($sku)
    {
        return $this->sendRequest('product/?sku=' . $sku, [], self::METHOD_DELETE);
    }

    /*************************
     * Order Endpoint
     ************************/

    /**
     * Retrieve details about an order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function getOrder($webshoporderid)
    {
        return $this->sendRequest('order' . $webshoporderid);
    }

    /**
     * Retrieve details about an order with a changed status since the provided date
     * @param $date
     * @return bool|string
     * @throws Exception
     */
    public function getUpdatedOrders($date)
    {
        return $this->sendRequest('order/updated_since/' . $date);
    }

    /**
     * Create an RMA link for an order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function createRMALink($webshoporderid)
    {
        return $this->sendRequest('order/' . $webshoporderid . '/rmalinks');
    }

    /**
     * Create an order
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createOrder($data)
    {
        return $this->sendRequest('order', [], self::METHOD_POST, $data);
    }

    /**
     * Update an order
     * @param $webshoporderid
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function updateOrder($webshoporderid, $data)
    {
        return $this->sendRequest('order/'. $webshoporderid, [], self::METHOD_PUT, $data);
    }

    /**
     * Delete a order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function deleteOrder($webshoporderid)
    {
        return $this->sendRequest('order/' . $webshoporderid, [], self::METHOD_DELETE);
    }

    /**
     * Get return forecasts for an order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function getOrderReturnForecasts($webshoporderid)
    {
        return $this->sendRequest('order/' . $webshoporderid . '/returnforecasts');
    }

    /**
     * Get returnlabels for an order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function getOrderReturnLabels($webshoporderid)
    {
        return $this->sendRequest('order/' . $webshoporderid . '/returnlabels');
    }

    /**
     * Create shippinglabels for an order
     * @param $webshoporderid
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createOrderShippingLabels($webshoporderid, $data)
    {
        return $this->sendRequest('order/' . $webshoporderid . '/shippinglabels', [], self::METHOD_POST, $data);
    }

    /**
     * Get shippinglabels for an order
     * @param $webshoporderid
     * @return bool|string
     * @throws Exception
     */
    public function getOrderShippingLabels($webshoporderid)
    {
        return $this->sendRequest('order/' . $webshoporderid . '/shippinglabels');
    }

    /*************************
     * Purchase Order Group Endpoint
     ************************/

    /**
     * Retrieve details about a purchase order group
     * @param $creationDate
     * @param $page
     * @return bool|string
     * @throws Exception
     */
    public function getPurchaseOrderGroup($creationDate, $page)
    {
        return $this->sendRequest('purchaseordergroup?creationDate='. $creationDate . '&page='. $page);
    }

    /*************************
     * Address Endpoint
     ************************/

    /**
     * Retrieve details about a purchase order group
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function validateAddress($data)
    {
        return $this->sendRequest('address', [], self::METHOD_POST, $data);
    }

    /*************************
     * Inbound Forecast Endpoint
     ************************/

    /**
     * Retrieve details of an inbound forecast based on reference and sku
     * @param $reference
     * @param $sku
     * @return bool|string
     * @throws Exception
     */
    public function getInboundForecastByReferenceAndSku($reference, $sku)
    {
        return $this->sendRequest('inboundforecast/group/'. $reference . '/' . $sku);
    }

    /**
     * Retrieve details of an inbound forecast based on reference
     * @param $reference
     * @return bool|string
     * @throws Exception
     */
    public function getInboundForecastByReference($reference)
    {
        return $this->sendRequest('inboundforecast/group/'. $reference);
    }

    /**
     * Create an inbound forecast in a group with reference
     * @param $reference
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createInboundForecast($reference, $data)
    {
        return $this->sendRequest('/inboundforecast/group/'. $reference, [], self::METHOD_POST, $data);
    }

    /**
     * Create an inbound forecast group based on reference
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createInboundForecastGroup($data)
    {
        return $this->sendRequest('/inboundforecast/group', [], self::METHOD_POST, $data);
    }

    /**
     * Update Inbound Forecast in a group
     * @param $reference
     * @param $sku
     * @param $data
     * @param bool $addQtyToExisting
     * @return bool|string
     * @throws Exception
     */
    public function updateInboundForecast($reference, $sku, $data, bool $addQtyToExisting = false)
    {
        if (!str_contains($sku, '/'))
        {
            return $this->sendRequest('/inboundforecast/group/' . $reference . '?sku=' . $sku . '&addQtyToExisting=' . $addQtyToExisting, [], self::METHOD_PUT, $data);
        } else {
            return $this->sendRequest('/inboundforecast/group/' . $reference . '/' . $sku . '/' . $addQtyToExisting, [], self::METHOD_PUT, $data);
        }
    }

    /**
     * Update an existing Inbound Forecast Group
     * @param $reference
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function updateInboundForecastGroup($reference, $data)
    {
        return $this->sendRequest('inboundforecast/group/' . $reference, [], self::METHOD_PUT, $data);
    }

    /**
     * Delete an Inbound Forecast from a group
     * @param $reference
     * @param $sku
     * @return bool|string
     * @throws Exception
     */
    public function deleteInboundForecast($reference, $sku)
    {
        return $this->sendRequest('inboundforecast/group/' . $reference . '/' . $sku, [], self::METHOD_DELETE);
    }

    /**
     * Delete an Inbound Forecast Group with all Inbound Forecasts
     * @param $reference
     * @return bool|string
     * @throws Exception
     */
    public function deleteInboundForecastGroup($reference)
    {
        return $this->sendRequest('inboundforecast/group/' . $reference, [], self::METHOD_DELETE);
    }

    /*************************
     * Inbounds Endpoint
     ************************/

    /**
     * Retrieve inbound
     * @param $id
     * @return bool|string
     * @throws Exception
     */
    public function getInbound($id)
    {
        return $this->sendRequest('inbounds?sinceid=' . $id);
    }

    /*************************
     * Inbounds Endpoint
     ************************/

    /**
     * Create Return Forecast
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function createReturnForecast($data)
    {
        return $this->sendRequest('returnforecast', [], self::METHOD_POST, $data);
    }

    /**
     * Update Return Forecast
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function updateReturnForecast($data)
    {
        return $this->sendRequest('returnforecast', [], self::METHOD_PUT, $data);
    }

    /**
     * Retrieve previously created Return Forecast by code
     * @param $code
     * @return bool|string
     * @throws Exception
     */
    public function getReturnForecast($code)
    {
        return $this->sendRequest('returnforecast/' . $code);
    }

    /*************************
     * Returnlabel Endpoint
     ************************/

    



    /*************************
     * Order Events Endpoint
     ************************/

    /**
     * Fetch latest order events in Monta
     * You can filter the latest events using the latest known event ID that you have synchronised.
     * It will exclude the ID you provided.
     * Maximum: 200 events per request
     * @param int $id
     * @return bool|string
     * @throws Exception
     */
    public function getOrderEvents(int $id)
    {
        return $this->sendRequest('orderevents/since_id/' . $id);
    }


}
