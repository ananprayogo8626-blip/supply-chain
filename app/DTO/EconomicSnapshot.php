<?php

namespace App\DTO;

/**
 * Plain (non-Eloquent) value object standing in for the old EconomicData model,
 * so Blade views written against $country->economicData->{field} keep working
 * with live World Bank API data instead of a persisted row.
 */
class EconomicSnapshot
{
    public function __construct(
        public readonly ?float $gdp = null,
        public readonly ?float $gdp_growth = null,
        public readonly ?float $inflation = null,
        public readonly ?float $exports = null,
        public readonly ?float $imports = null,
        public readonly ?float $trade_balance = null,
        public readonly ?int $population = null,
        public readonly ?int $data_year = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            gdp: isset($data['gdp']) ? (float) $data['gdp'] : null,
            gdp_growth: isset($data['gdp_growth']) ? (float) $data['gdp_growth'] : null,
            inflation: isset($data['inflation']) ? (float) $data['inflation'] : null,
            exports: isset($data['exports']) ? (float) $data['exports'] : null,
            imports: isset($data['imports']) ? (float) $data['imports'] : null,
            trade_balance: isset($data['trade_balance']) ? (float) $data['trade_balance'] : null,
            population: isset($data['population']) ? (int) $data['population'] : null,
            data_year: isset($data['data_year']) ? (int) $data['data_year'] : null,
        );
    }
}
