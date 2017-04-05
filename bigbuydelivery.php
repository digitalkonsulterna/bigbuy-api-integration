<?php

    $token = 'OTMxMmU1MTQ3MDhhY2IxMmZlMDBiYmFlNjc1YTlhYTBlMTZkYTc1NWYwYzJiZjhiNGJkOGExMzZkYzY5OTAwNA';
    $delivery = array();

    $delivery['order']['delivery'] = array(
       'isoCountry' => 'ES',
       'postcode' => 46023,
    );
    $delivery['order']['products'] = array(
        0 => array(
           'reference' => 'V0100100',
           'quantity' => 1,
        ),
        1 => array(
           'reference' => 'F1505138',
           'quantity' => 4,
        ),
      );

    // print_r(json_encode($bigbuy));

    $ch = curl_init("http://api.sandbox.bigbuy.eu/rest/shipping/orders.json");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));

    $result = curl_exec($ch);
    $object = json_decode($result);

    $shipping_options_cost = array();
    $shipping_options = array();

    foreach ($object->shippingOptions as $key => $value) {
      array_push($shipping_options_cost, $value->cost);
      array_push($shipping_options, $value);
    }

    $min_x =  min($shipping_options_cost);

    foreach ($shipping_options as $key => $value) {
      if($value->cost === $min_x){
        // print_r($value);
      }
    }




