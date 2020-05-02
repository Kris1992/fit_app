<?php 

namespace App\Services\WeatherService;

/** 
 *  Weather Service Interface
 */
interface WeatherServiceInterface
{

    /**
     * getTemperature Get temperature in given by coords location and date 
     * @param  Array  $location Coordinates of location
     * @param  DateTimeInterface $date Date of workout   
     * @return Array|null
     */
    public function getWeather(Array $location, \DateTimeInterface $date): ?Array;
    
}
