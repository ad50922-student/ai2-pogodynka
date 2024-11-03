<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    /**
     * @dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit(string $celsius, float $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);
        $this->assertSame($expectedFahrenheit, $measurement->getFahrenheit(), "Expected Fahrenheit for {$celsius} Celsius");
    }

    public function dataGetFahrenheit(): array
    {
        return [
            ['0', 32],
            ['-100', -148],
            ['100', 212],
            ['0.5', 32.9],
            ['-40', -40], // -40°C = -40°F (przykład ułamka)
            ['25', 77],   // 25°C = 77°F
            ['37.5', 99.5], // 37.5°C = 99.5°F (przykład ułamka)
            ['10', 50],   // 10°C = 50°F
            ['-10', 14],  // -10°C = 14°F
            ['15.2', 59.36], // 15.2°C = 59.36°F (przykład ułamka)
        ];
    }
}
