<?php

namespace Tests\Unit;

use App\Services\BulkImportService;
use PHPUnit\Framework\TestCase;

class BulkImportServiceTest extends TestCase
{
    public function test_can_parse_specs_semicolon_format(): void
    {
        $service = new BulkImportService;
        $specs = $service->parseSpecsString('Material: Greyboard; Finishing: Matte Lamination; GSM: 350');

        $this->assertCount(3, $specs);
        $this->assertSame('Material', $specs[0]['key']);
        $this->assertSame('Greyboard', $specs[0]['value']);
        $this->assertSame('Finishing', $specs[1]['key']);
        $this->assertSame('Matte Lamination', $specs[1]['value']);
        $this->assertSame('GSM', $specs[2]['key']);
        $this->assertSame('350', $specs[2]['value']);
    }

    public function test_can_parse_specs_json_format(): void
    {
        $service = new BulkImportService;
        $json = json_encode([
            ['key' => 'Flute', 'value' => 'BC Flute'],
            ['key' => 'Ply', 'value' => '5-Ply'],
        ]);

        $specs = $service->parseSpecsString($json);
        $this->assertCount(2, $specs);
        $this->assertSame('Flute', $specs[0]['key']);
        $this->assertSame('BC Flute', $specs[0]['value']);
    }

    public function test_can_parse_csv_file(): void
    {
        $service = new BulkImportService;
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_test_');

        $content = "name,category,job_number,short_description\n";
        $content .= "\"Test Product A\",\"Boxes\",\"JB-01\",\"A great box\"\n";
        $content .= "\"Test Product B\",\"Cartons\",\"JB-02\",\"Another carton\"\n";

        file_put_contents($tempFile, $content);

        $rows = $service->parseCsv($tempFile);
        @unlink($tempFile);

        $this->assertCount(2, $rows);
        $this->assertSame('Test Product A', $rows[0]['name']);
        $this->assertSame('Boxes', $rows[0]['category']);
        $this->assertSame('JB-01', $rows[0]['job_number']);
        $this->assertSame('A great box', $rows[0]['short_description']);
    }
}
