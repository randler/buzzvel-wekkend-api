<?php

namespace WeekBuzz;

use WeekBuzz\Anonymous;

class Routes
{
    
    /**
     * @return \Adiq\Anonymous
     */
    public static function hotels()
    {
        $anonymous = new Anonymous();

        $anonymous->list = static function () {
            return 'hotels.json';
        };

        return $anonymous;
    }
}
