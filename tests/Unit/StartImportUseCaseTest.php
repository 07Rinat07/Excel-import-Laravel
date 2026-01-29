<?php

namespace Tests\Unit;

use App\Application\Import\DTO\StartImportRequest;
use App\Application\Import\UseCases\StartImportUseCase;
use App\Domain\Import\Exceptions\InvalidFileException;
use App\Domain\Import\Repositories\ImportTaskRepository;
use App\Domain\Import\Repositories\ImportedRowRepository;
use App\Domain\Import\Services\ExcelParserService;
use App\Domain\Import\Services\ValidationService;
use Tests\TestCase;

class StartImportUseCaseTest extends TestCase
{
    public function test_throws_when_file_missing(): void
    {
        $useCase = new StartImportUseCase(
            $this->createMock(ImportTaskRepository::class),
            $this->createMock(ImportedRowRepository::class),
            $this->createMock(ExcelParserService::class),
            $this->createMock(ValidationService::class),
        );

        $request = new StartImportRequest(
            userId: 1,
            templateId: 1,
            filePath: '/tmp/does-not-exist.xlsx',
            fileName: 'does-not-exist.xlsx',
            mappingStrategy: 'manual'
        );

        $this->expectException(InvalidFileException::class);
        $useCase->execute($request);
    }
}
