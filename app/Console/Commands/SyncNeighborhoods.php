<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncNeighborhoods extends Command
{
    protected $signature = 'neighborhoods:sync';
    protected $description = 'Fetches all LA neighborhoods from Overpass and generates neighborhoods.json';

    protected $overpassUrl;

    public function handle()
    {
        $this->overpassUrl = Config::get('services.overpass.url');
        $this->info("Fetching LA neighborhoods...");

        $response = Http::timeout(20)->retry(3, 300)->get($this->overpassUrl);

        if ($response->failed()) {
            $this->error("Failed to fetch Overpass API.");
            return 1;
        }

        $json = $response->json();
        if (!isset($json['elements'])) {
            $this->error("Invalid format from Overpass API.");
            return 1;
        }

        $neighborhoods = [];

        foreach ($json['elements'] as $el) {
            if (!isset($el['tags']['name'])) {
                continue;
            }

            $name = $el['tags']['name'];
            $lat  = $el['lat'] ?? ($el['center']['lat'] ?? null);
            $lon  = $el['lon'] ?? ($el['center']['lon'] ?? null);

            if (!$lat || !$lon) {
                continue;
            }

            $neighborhoods[$name] = [
                'lat' => $lat,
                'lon' => $lon,
            ];
        }

        if (count($neighborhoods) < 20) {
            $this->error("WARNING: too few neighborhoods received.");
        }

        Storage::put(
            'neighborhoods.json',
            json_encode($neighborhoods, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->info("Generated storage/app/neighborhoods.json with " . count($neighborhoods) . " neighborhoods.");
        return 0;
    }
}
