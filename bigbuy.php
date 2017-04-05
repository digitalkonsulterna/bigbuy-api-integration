<?php

    $token = 'OTMxMmU1MTQ3MDhhY2IxMmZlMDBiYmFlNjc1YTlhYTBlMTZkYTc1NWYwYzJiZjhiNGJkOGExMzZkYzY5OTAwNA';

    $bigbuy['order'] = array(
     'internalReference' => 'test',
     'cashOnDelivery' => 0,
     'language' => 'es',
     'paymentMethod' => 'moneybox',
     'carriers' =>
        array (
          0 => array(
             'name' => 'correos',
          ),
          1 =>
          array(
             'name' => 'chrono',
          ),
        ),
    );

    $bigbuy['order']['shippingAddress'] = array(
           'firstName' => 'test',
           'lastName' => 'test',
           'country' => 'ES',
           'postcode' => '46023',
           'town' => 'test',
           'address' => 'test',
           'phone' => 'test',
           'email' => 'test',
           'comment' => '',
        );

    $bigbuy['order']['products'] = array(
        0 => array(
           'reference' => 'V0100100',
           'quantity' => 1,
        ),
        1 => array(
           'reference' => 'F1505138',
           'quantity' => 4,
        ),
      );

    print_r(json_encode($bigbuy));

    $ch = curl_init("http://api.sandbox.bigbuy.eu/rest/order/create.json");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bigbuy));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

    $result = curl_exec($ch);
    // print_r($result);


