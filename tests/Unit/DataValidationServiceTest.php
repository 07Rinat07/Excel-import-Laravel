<?php

namespace Tests\Unit;

use App\Services\DataValidationService;
use Tests\TestCase;

class DataValidationServiceTest extends TestCase
{
    public function test_validates_rules_for_row(): void
    {
        $service = new DataValidationService();

        $columns = [
            [
                'key' => 'email',
                'label' => 'Email',
                'validation_rules' => ['required', 'email'],
            ],
        ];

        $invalid = $service->validateRow(['email' => 'not-an-email'], $columns);
        $this->assertFalse($invalid['valid']);
        $this->assertArrayHasKey('email', $invalid['errors']);

        $valid = $service->validateRow(['email' => 'test@example.com'], $columns);
        $this->assertTrue($valid['valid']);
    }
}
