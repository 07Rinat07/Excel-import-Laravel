<?php

namespace Tests\Unit;

use App\Models\ExcelTemplateColumn;
use App\Services\SmartMappingService;
use Tests\TestCase;

class SmartMappingServiceTest extends TestCase
{
    public function test_suggests_mapping_for_similar_headers(): void
    {
        $service = new SmartMappingService();

        $email = new ExcelTemplateColumn(['key' => 'email', 'label' => 'Email']);
        $email->id = 1;
        $name = new ExcelTemplateColumn(['key' => 'name', 'label' => 'Name']);
        $name->id = 2;

        $columns = collect([$email, $name]);

        $result = $service->suggestMapping(['Email', 'Name'], $columns);

        $this->assertSame(1, $result['mapping'][0]);
        $this->assertSame(2, $result['mapping'][1]);
        $this->assertGreaterThan(0, $result['confidence']['matched_headers']);
    }
}
