<?php

namespace WeekBuzz;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as ClientException;
use WeekBuzz\Endpoints\Hotels;
use WeekBuzz\Exceptions\InvalidJsonException;

class Search
{
    /**
     * @var string
     */
    const BASE_URI = 'https://buzzvel-interviews.s3.eu-west-1.amazonaws.com/';
    
    /**
     * @var string
     */
    const URI_HOTELS = self::BASE_URI . "hotels.json";

    /**
     * @var string
     */
    const GET = 'GET';

    /**
     * @author: Randler Ferraz Almeida
     * @method: GET
     * 
     * @param $latitude
     * @param $longitude
     * @param $orderBy (proximity or pricepernight)
     * 
     * @return HTML List of Hotels
     * 
     */
    public static function getNearbyHotels( $latitude, $longitude, $orderBy = "proximity" )
    {
        // get hotels
        $hotels = self::getHotels();

        // order hotels
        $hotels = self::orderHotels($hotels, $latitude, $longitude, $orderBy);
        
        $out = "<ul>";

        //loop through the array and concatenate the list
        array_map(function($hotel) use (&$out) {
            $out .= "<li>{$hotel[0]} - {$hotel[3]}</li>";
        }, $hotels);

        $out .= "</ul>";


        return $out;

    }

    /**
     * @author: Randler Ferraz Almeida
     * @method: GET
     * 
     * @param $latitude
     * @param $longitude
     * @param $orderBy (proximity or pricepernight)
     * 
     * @return ArrayObject of Hotels
     * 
     */
    public static function getNearbyHotelsArray( $latitude, $longitude, $orderBy = "proximity" )
    {
        // get hotels
        $hotels = self::getHotels();
        // order hotels
        $hotels = self::orderHotels($hotels, $latitude, $longitude, $orderBy);

        return $hotels;

    }

    // Calculate distance between two points in latitude and longitude in km's or miles
    public static function distance(
        $lat1, 
        $lon1, 
        $lat2, 
        $lon2, 
        $unit = "km"
    ) 
    {    
        $lat1 = floatval($lat1); 
        $lon1 = floatval($lon1); 
        $lat2 = floatval($lat2); 
        $lon2 = floatval($lon2);

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "KM") {
            return number_format(($miles * 1.609344), 2, '.', '') . " KM";
        } else if ($unit == "M") {
            return number_format(($miles * 0.8684), 2) . " M";
        } else {
            return number_format($miles, 2);
        }
    }

    //make request hotels
    public static function getHotels()
    {
        $client = new HttpClient();
        $response = $client->request(self::GET, self::URI_HOTELS);
        $hotels = json_decode($response->getBody());
        
        if(!$hotels->success) {
            throw new Exception("Oops Hotels not found!", 404);
        }

        $hotels = $hotels->message;

        return $hotels;
    }

    //order hotels
    public static function orderHotels( $hotels, $latitude, $longitude, $orderBy )
    {
        $orderBy = isset($orderBy) && !empty($orderBy) ? $orderBy : "proximity";
        if( $orderBy == 'proximity' ) {
            // sort array by distance
            usort($hotels, function($a, $b) use ($latitude, $longitude) {
                $a = self::distance($latitude, $longitude, $a[1], $a[2]);
                $b = self::distance($latitude, $longitude, $b[1], $b[2]);
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
        } else if( $orderBy == 'pricepernight' ) {
            // sort array by price per night
            usort($hotels, function($a, $b) {
                if ($a[3] == $b[3]) {
                    return 0;
                }
                return ($a[3] < $b[3]) ? -1 : 1;
            });
        }
        return $hotels;
    }
}
