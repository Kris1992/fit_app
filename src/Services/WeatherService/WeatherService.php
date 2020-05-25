<?php 
declare(strict_types=1);

namespace App\Services\WeatherService;

class WeatherService implements WeatherServiceInterface
{

    private $weather_api_key;
    private $format = 'json';

    public function __construct(string $weather_api_key)
    {
        $this->weather_api_key = $weather_api_key;
    }

    public function getWeather(Array $location, \DateTimeInterface $date): ?Array
    {

        $stopDate = \DateTimeImmutable::createFromMutable($date);
        $startDate = $stopDate->modify('-1 day');
        
        $urlTemplate = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/weatherdata/history?&aggregateHours=24&startDateTime=%s&endDateTime=%s&unitGroup=metric&contentType=%s&dayStartTime=0:0:00&dayEndTime=0:0:00&location=%s,%s&key=%s&shortColumnNames=true';
        $url = sprintf($urlTemplate, $startDate->format('Y-m-d\TH:i:s'), $stopDate->format('Y-m-d\TH:i:s'), $this->format, $location['lat'], $location['lng'], $this->weather_api_key);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);   
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);  
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($result, true);
        if($data !== null) {  
            $locationData = reset($data['locations']);
            $temperature = $locationData['values'][0]['temp'];
            $weatherConditions = $locationData['values'][0]['conditions'];

            if ($temperature && $weatherConditions) {
                return [
                    'temperature' => intval($temperature),
                    'weatherConditions' => $weatherConditions
                ];
            }
        }

        return null;
    }  

}