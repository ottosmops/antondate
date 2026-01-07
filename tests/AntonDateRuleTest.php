<?php

namespace Ottosmops\Antondate\Tests;

use DateTimeImmutable;
use Ottosmops\Antondate\Rules\AntonDateRule;
use Ottosmops\Antondate\ValueObjects\AntonDate;

class AntonDateRuleTest extends TestCase
{
    public function testPassesWithAntonDateInstance(): void
    {
        $rule = new AntonDateRule();
        $antonDate = AntonDate::createFromString('1973-12-01');
        
        $this->assertTrue($rule->passes('date', $antonDate));
    }

    public function testPassesWithDateTimeImmutableInstance(): void
    {
        $rule = new AntonDateRule();
        $dateTime = new DateTimeImmutable('1973-12-01');
        
        $this->assertTrue($rule->passes('date', $dateTime));
    }

    public function testPassesWithValidStringInStrictMode(): void
    {
        $rule = new AntonDateRule(strict: true);
        
        $this->assertTrue($rule->passes('date', '1973-12-01'));
        $this->assertTrue($rule->passes('date', 'ca. 1973-12-01'));
        $this->assertTrue($rule->passes('date', '1973'));
        $this->assertTrue($rule->passes('date', '1973-12'));
    }

    public function testFailsWithInvalidStringInStrictMode(): void
    {
        $rule = new AntonDateRule(strict: true);
        
        $this->assertFalse($rule->passes('date', '4. Mai 1905'));
        $this->assertFalse($rule->passes('date', 'Mai 1905'));
        $this->assertFalse($rule->passes('date', '1973-13-01'));
    }

    public function testPassesWithGuessableStringInNonStrictMode(): void
    {
        $rule = new AntonDateRule(strict: false);
        
        $this->assertTrue($rule->passes('date', '4. Mai 1905'));
        $this->assertTrue($rule->passes('date', '1973-12-01'));
        $this->assertTrue($rule->passes('date', 'ca. 1973-12-01'));
    }

    public function testFailsWithInvalidStringInNonStrictMode(): void
    {
        $rule = new AntonDateRule(strict: false);
        
        $this->assertFalse($rule->passes('date', 'invalid date'));
        $this->assertFalse($rule->passes('date', 'abc'));
    }

    public function testPassesWithIntegerYear(): void
    {
        $rule = new AntonDateRule();
        
        $this->assertTrue($rule->passes('date', 1973));
    }

    public function testFailsWithNonStringNonIntValue(): void
    {
        $rule = new AntonDateRule();
        
        $this->assertFalse($rule->passes('date', ['1973']));
        $this->assertFalse($rule->passes('date', null));
        $this->assertFalse($rule->passes('date', 12.5));
    }

    public function testDefaultIsStrictMode(): void
    {
        $rule = new AntonDateRule();
        
        // This should fail in strict mode but would pass in non-strict
        $this->assertFalse($rule->passes('date', '4. Mai 1905'));
    }

    public function testMessageReturnsExpectedString(): void
    {
        $rule = new AntonDateRule();
        
        $this->assertEquals(':attribute is not an AntonDate.', $rule->message());
    }

    public function testToStringReturnsAntonDate(): void
    {
        $rule = new AntonDateRule();
        
        $this->assertEquals('AntonDate', (string) $rule);
    }
}
