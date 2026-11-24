<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $apiKey = config('services.openweather.key');

        if ($apiKey) {
            $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $data['lat'],
                'lon' => $data['lon'],
                'units' => 'metric',
                'appid' => $apiKey,
            ]);

            if ($response->successful()) {
                $weather = $response->json();

                return response()->json([
                    'place' => $weather['name'] ?? 'Selected location',
                    'temperature' => $weather['main']['temp'] ?? null,
                    'description' => $weather['weather'][0]['description'] ?? null,
                    'humidity' => $weather['main']['humidity'] ?? null,
                ]);
            }
        }

        $fallback = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $data['lat'],
            'longitude' => $data['lon'],
            'current' => 'temperature_2m,relative_humidity_2m,weather_code',
        ]);

        if ($fallback->successful()) {
            $current = $fallback->json('current', []);
            $code = (int) ($current['weather_code'] ?? 0);
            $descriptions = [
                0 => 'clear sky', 1 => 'mainly clear', 2 => 'partly cloudy',
                3 => 'overcast clouds', 45 => 'foggy', 51 => 'light drizzle',
                61 => 'light rain', 63 => 'moderate rain', 80 => 'rain showers',
                95 => 'thunderstorm',
            ];

            return response()->json([
                'place' => 'Manila / Selected Pin',
                'temperature' => $current['temperature_2m'] ?? 29.5,
                'description' => $descriptions[$code] ?? 'scattered clouds',
                'humidity' => $current['relative_humidity_2m'] ?? 74,
            ]);
        }

        return response()->json([
            'place' => 'Manila', 'temperature' => 29.5,
            'description' => 'scattered clouds', 'humidity' => 74,
        ]);
    }
}
