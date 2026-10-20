<?php

namespace Tests\Unit;

use App\Support\SalaryEstimate;
use PHPUnit\Framework\TestCase;

class SalaryEstimateTest extends TestCase
{
    public function test_it_estimates_hourly_usd_from_annual_array(): void
    {
        $hourly = SalaryEstimate::estimateHourlyUsd(['min' => 50000, 'max' => 80000]);

        $this->assertNotNull($hourly);
        $this->assertEquals(24, $hourly['min']);
        $this->assertEquals(38, $hourly['max']);
    }

    public function test_it_parses_explicit_hourly_strings(): void
    {
        $hourly = SalaryEstimate::estimateHourlyUsd(null, '$50-$75 /hour');

        $this->assertNotNull($hourly);
        $this->assertEquals(50, $hourly['min']);
        $this->assertEquals(75, $hourly['max']);

        $single = SalaryEstimate::estimateHourlyUsd(null, '$20/hr');
        $this->assertNotNull($single);
        $this->assertEquals(20, $single['min']);
        $this->assertEquals(20, $single['max']);
    }

    public function test_it_parses_k_annual_notation_strings(): void
    {
        $hourly = SalaryEstimate::estimateHourlyUsd(null, '$35,3k- $52k');

        $this->assertNotNull($hourly);
        $this->assertEquals(17, $hourly['min']);
        $this->assertEquals(25, $hourly['max']);
    }

    public function test_it_formats_hourly_range_and_single_rates(): void
    {
        $this->assertEquals('$20 - $35/hr', SalaryEstimate::formatHourly(['min' => 20, 'max' => 35]));
        $this->assertEquals('$25/hr', SalaryEstimate::formatHourly(['min' => 25, 'max' => 25]));
        $this->assertNull(SalaryEstimate::formatHourly(null));
    }
}
