<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SensorInfoController extends Controller
{
    public function handle()
    {
        // Send GET request to the "http://".config('sensor.ip') URL, receiving JSON data
        $dataset = $this->getDatasetFromSensor();
        if ($dataset === null) {
            $this->error('Failed to retrieve data from the sensor.');
            return;
        }
        
        $cleanDataset = $this->extractNotCorruptedData($dataset);
        $locationDataset = $this->extractLocationData($cleanDataset);
        $locationExactDataset = $this->fulfillLocationDataset($locationDataset);
        return response()->json($locationExactDataset[0]);
    }

    private function getDatasetFromSensor(){
        $url = config('sensor.id');
        $client = new Client();
        $response = $client->get($url)->getBody()->getContents();
        $data = json_decode($response, true);
        if(!isset($data['bufor'])) {
            return null;
        }
        return $data['bufor'];
    }

    private function extractNotCorruptedData($data)
    {
        $notCorrupted = [];
        $data = explode("\r\n", $data);
        foreach ($data as $value) {
            // Test if the value is correct JSON:
            if (json_decode($value) !== null) {
                $notCorrupted[] = json_decode($value, true);
            } else {
                // If the value is not valid JSON, skip it
                continue;
            }
            
        }
        return array_reverse($notCorrupted); // Reverse the array to get the latest data first
    }

    public function extractLocationData($cleanDataset) {
        $locationDataset = [];
        
        foreach ($cleanDataset as $entry) {
            if (isset($entry['GPGLL'])) {
                $locationDataset[] = $entry['GPGLL'];
            }
        }
        
        return $locationDataset;
    }

    public function fulfillLocationDataset($locationDataset) {
        foreach ($locationDataset as &$location) {
            $latitude = $this->convertToDecimal($location['latitude'], $location['latitude_hemisphere']);
            $longitude = $this->convertToDecimal($location['longitude'], $location['longitude_hemisphere']);
            $location['latitude_decimal'] = round($latitude, 7);
            $location['longitude_decimal'] = round($longitude, 7);
            
            $location['complex'] = sprintf("%.5f %s, %.5f %s", 
                abs($latitude), $location['latitude_hemisphere'], 
                abs($longitude), $location['longitude_hemisphere']
            );
        }
        return $locationDataset;
    }

    private function convertToDecimal($coordinate, $hemisphere) {
        // Rozdziel stopnie i minuty
        $degrees = floor($coordinate / 100);
        $minutes = $coordinate - ($degrees * 100);
        
        // Przekształć na system dziesiętny
        $decimal = $degrees + ($minutes / 60);
        
        // Ustal znak na podstawie półkuli
        if ($hemisphere === 'S' || $hemisphere === 'W') {
            $decimal *= -1;
        }
        
        return $decimal;
    }
    
}
