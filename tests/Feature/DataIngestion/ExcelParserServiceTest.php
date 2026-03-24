<?php

namespace Tests\Feature\DataIngestion;

use App\Services\DataIngestion\Parsers\ExcelParserService;
use App\Services\DataIngestion\Parsers\Exceptions\ParserException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelParserServiceTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir();
    }

    protected function createExcelFile(array $headers, array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write headers
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }

        // Write data rows
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $col => $value) {
                $sheet->setCellValue(chr(65 + $col) . ($rowIndex + 2), $value);
            }
        }

        $filePath = $this->tempDir . '/test_import_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    public function test_parses_valid_excel_with_required_columns(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand', 'Size (ml)', 'Price', 'Currency'],
            [
                ['Sauvage Elixir', 'Dior', '100', '15000', 'INR'],
                ['Bleu de Chanel', 'Chanel', '50', '12000', 'INR'],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(2, $result);
        $this->assertEquals('Sauvage Elixir', $result[0]['perfume_name']);
        $this->assertEquals('Dior', $result[0]['brand']);
        $this->assertCount(1, $result[0]['prices']);
        $this->assertEquals(15000, $result[0]['prices'][0]['price']);
        $this->assertEquals(100, $result[0]['prices'][0]['size_ml']);

        $this->assertEmpty($parser->getErrors());

        unlink($filePath);
    }

    public function test_parses_optional_columns(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand', 'Size (ml)', 'Price', 'Currency', 'Concentration', 'Gender Affinity', 'Stock Status'],
            [
                ['Aventus', 'Creed', '100', '25000', 'INR', 'EDP', 'Male', 'In Stock'],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(1, $result);
        $this->assertEquals('EDP', $result[0]['concentration']);
        $this->assertEquals('Male', $result[0]['gender_affinity']);
        $this->assertEquals('In Stock', $result[0]['prices'][0]['stock_status']);

        unlink($filePath);
    }

    public function test_applies_defaults_for_missing_optional_values(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand', 'Size (ml)', 'Price', 'Currency', 'Stock Status', 'Item Type'],
            [
                ['Test Perfume', 'Test Brand', '50', '5000', 'INR', '', ''],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(1, $result);
        $this->assertEquals('In Stock', $result[0]['prices'][0]['stock_status']);
        $this->assertEquals('Full Bottle', $result[0]['prices'][0]['item_type']);

        unlink($filePath);
    }

    public function test_throws_exception_for_missing_required_headers(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand'], // Missing Size, Price, Currency
            [
                ['Test', 'Brand'],
            ]
        );

        $parser = new ExcelParserService();

        $this->expectException(ParserException::class);
        $parser->parse($filePath);

        unlink($filePath);
    }

    public function test_throws_exception_for_invalid_file_path(): void
    {
        $parser = new ExcelParserService();

        $this->expectException(ParserException::class);
        $parser->parse('/nonexistent/path/file.xlsx');
    }

    public function test_handles_case_insensitive_headers(): void
    {
        $filePath = $this->createExcelFile(
            ['PERFUME NAME', 'BRAND', 'SIZE (ML)', 'PRICE', 'CURRENCY'],
            [
                ['Test Perfume', 'Test Brand', '100', '5000', 'INR'],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(1, $result);
        $this->assertEquals('Test Perfume', $result[0]['perfume_name']);

        unlink($filePath);
    }

    public function test_strips_trailing_asterisk_from_headers(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name*', 'Brand*', 'Size (ml)*', 'Price*', 'Currency*'],
            [
                ['Test Perfume', 'Test Brand', '100', '5000', 'INR'],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(1, $result);
        $this->assertEquals('Test Perfume', $result[0]['perfume_name']);

        unlink($filePath);
    }

    public function test_skips_rows_with_missing_required_values(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand', 'Size (ml)', 'Price', 'Currency'],
            [
                ['Valid Perfume', 'Valid Brand', '100', '5000', 'INR'],
                ['', 'Missing Name', '50', '3000', 'INR'], // Missing required name
                ['Another Valid', 'Brand', '75', '8000', 'INR'],
            ]
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertCount(2, $result);
        $this->assertNotEmpty($parser->getErrors());

        unlink($filePath);
    }

    public function test_returns_empty_for_header_only_file(): void
    {
        $filePath = $this->createExcelFile(
            ['Perfume Name', 'Brand', 'Size (ml)', 'Price', 'Currency'],
            [] // No data rows
        );

        $parser = new ExcelParserService();
        $result = $parser->parse($filePath);

        $this->assertEmpty($result);

        unlink($filePath);
    }

    public function test_get_source_identifier(): void
    {
        $parser = new ExcelParserService();
        $this->assertEquals('excel_import_standard_v1', $parser->getSourceIdentifier());
    }
}
