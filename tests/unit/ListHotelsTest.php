<?php
namespace WeekBuzz\Test;

require_once ('vendor/autoload.php');

/** Testes
 * ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/unit/ListHotelsTest.php
 */
use PHPUnit\Framework\TestCase;
use WeekBuzz\Search;

class ListHotelsTest extends TestCase
{
    public function testRequestAuth()
    {        
        $my_lat = "-34.596111224556964";
        $my_lng = "-58.51730236052004";

        $hotels = Search::getNearbyHotels($my_lat, $my_lng, "'pricepernight'");
        
        // Requisição de token funcionando
        //$hotels = $search->hotels()->list();

        fwrite(STDERR, print_r($hotels));
        
    }
}
