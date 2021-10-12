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
        $search = new Search();
        
        // Requisição de token funcionando
        $hotels = $search->hotels()->list();

        fwrite(STDERR, print_r($hotels));
        
    }
}
