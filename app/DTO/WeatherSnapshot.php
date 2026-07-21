<?php

namespace App\DTO;

/**
 * Plain (non-Eloquent) value object standing in for the old WeatherData model,
 * so Blade views written against $country->weatherData->{field} keep working
 * with live API data instead of a persisted row.
 */
class WeatherSnapshot
{
    public function __construct(
        public readonly ?float $temperature = null,
        public readonly ?float $humidity = null,
        public readonly ?float $wind_speed = null,
        public readonly ?float $rainfall = null,
        public readonly ?float $cloud = null,
        public readonly ?float $pressure = null,
        public readonly ?string $weather_condition = null,
        public readonly ?int $storm_risk = null,
        public readonly mixed $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            temperature: isset($data['temperature']) ? (float) $data['temperature'] : null,
            humidity: isset($data['humidity']) ? (float) $data['humidity'] : null,
            wind_speed: isset($data['wind_speed']) ? (float) $data['wind_speed'] : null,
            rainfall: isset($data['rainfall']) ? (float) $data['rainfall'] : null,
            cloud: isset($data['cloud']) ? (float) $data['cloud'] : null,
            pressure: isset($data['pressure']) ? (float) $data['pressure'] : null,
            weather_condition: $data['weather_condition'] ?? null,
            storm_risk: isset($data['storm_risk']) ? (int) $data['storm_risk'] : null,
            updated_at: $data['updated_at'] ?? now(),
        );
    }
}
