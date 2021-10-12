<?php

namespace WeekBuzz;

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
     * @var string header used to identify application's requests
     */
    const DELIVERY_USER_AGENT_HEADER = 'X-WeekBuzz-User-Agent';

    /**
     * @var \GuzzleHttp\Client
     */
    private $http;

    /**
     * @var \WeekBuzz\Endpoints\Payment
     */
    private $hotels;
    
    /**
     * @param string $apiKey
     * @param array|null $extras
     * @param boolean|false $test
     */
    public function __construct(array $extras = null)
    {
            $base_url = self::BASE_URI;

        $options = ['base_uri' => $base_url];

        if (!is_null($extras)) {
            $options = array_merge($options, $extras);
        }

        $userAgent = isset($options['headers']['User-Agent']) ?
            $options['headers']['User-Agent'] :
            '';
        $authorization = isset($extras['Authorization']) ?
            $extras['Authorization'] :
            '';

        $options['headers'] = $this->addUserAgentHeaders($userAgent, $authorization);

        $this->http = new HttpClient($options);

        $this->hotels = new Hotels($this);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @throws \WeekBuzz\Exceptions\WeekBuzzException
     * @return \ArrayObject
     *
     * @psalm-suppress InvalidNullableReturnType
     */
    public function request($method, $uri, $options = [], $header = [])
    {
        try {

            $userAgent = isset($header['headers']['User-Agent']) ?
                $header['headers']['User-Agent'] :
                '';
            if(isset($header) && !empty($header)) {
                $base_url = self::BASE_URI;
        
                $options = array_merge($options, ['base_uri' => $base_url]);

                $authorization = isset($header['Authorization']) ?
                    $header['Authorization'] :
                    '';

                $options['headers'] = $this->addUserAgentHeaders($userAgent, $authorization);

                $this->http = new HttpClient($options);
            }
            
            $response = $this->http->request(
                $method,
                $uri,
                $options
            );

            $body = ResponseHandler::success((string)$response->getBody());

            return $body;
        } catch (InvalidJsonException $exception) {
            throw $exception;
        } catch (ClientException $exception) {
            ResponseHandler::failure($exception);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Build an user-agent string to be informed on requests
     *
     * @param string $customUserAgent
     *
     * @return string
     */
    private function buildUserAgent($customUserAgent = '')
    {
        return trim(sprintf(
            '%s PHP/%s',
            $customUserAgent,
            phpversion()
        ));
    }

    /**
     * Append new keys (the default and delivery) related to user-agent
     *
     * @param string $customUserAgent
     * @return array
     */
    private function addUserAgentHeaders($customUserAgent = '', $authorization = null)
    {
        return [
            'User-Agent' => $this->buildUserAgent($customUserAgent),
            'Content-Type' => "application/json",
            'Authorization' => $authorization,
            self::DELIVERY_USER_AGENT_HEADER => $this->buildUserAgent(
                $customUserAgent
            )
        ];
    }

    /**
     * @return \WeekBuzz\Endpoints\Payment
     */
    public function hotels()
    {
        return $this->hotels;
    }

    public static function getNearbyHotels( $latitude, $longitude, $orderby = 'proximity' )
    {
        //“proximity” or “pricepernight”
        $url = 'https://buzzvel-interviews.s3.eu-west-1.amazonaws.com/hotels.json';
        $client = new HttpClient();
        $response = $client->request('GET', $url);
        $hotels = json_decode($response->getBody());
        $hotels = $hotels->message;


        if( $orderby == 'proximity' ) {
        // sort array by distance
            usort($hotels, function($a, $b) use ($latitude, $longitude) {
                $a = self::distance($latitude, $longitude, $a[1], $a[2]);
                $b = self::distance($latitude, $longitude, $b[1], $b[2]);
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
        } else if( $orderby == 'pricepernight' ) {
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
}
