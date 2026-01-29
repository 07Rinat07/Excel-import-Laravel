<?php

declare(strict_types=1);

namespace App\Application\Import\UseCases;

use App\Application\Import\DTO\StartImportRequest;
use App\Application\Import\DTO\StartImportResponse;
use App\Domain\Import\Repositories\ImportTaskRepository;
use App\Domain\Import\Services\ExcelParserService;
use App\Domain\Import\Services\ValidationService;
use App\Domain\Import\Entities\ImportTask;
use App\Domain\Import\Entities\ImportedRow;
use App\Domain\Import\Repositories\ImportedRowRepository;
use App\Domain\Import\ValueObjects\ImportTaskId;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Domain\Import\ValueObjects\FileContent;
use App\Domain\Import\ValueObjects\MappingStrategy;
use App\Domain\Import\ValueObjects\RowNumber;
use App\Domain\Import\Exceptions\InvalidFileException;
use App\Domain\Import\Exceptions\ValidationFailedException;

/**
 * Use Case: StartImportUseCase
 *
 * Orchestrates the import process:
 * 1. Parse Excel file
 * 2. Validate rows
 * 3. Create ImportTask
 * 4. Store validated rows
 * 5. Return response
 */
final class StartImportUseCase
{
    public function __construct(
        private readonly ImportTaskRepository $importTaskRepository,
        private readonly ImportedRowRepository $importedRowRepository,
        private readonly ExcelParserService $excelParserService,
        private readonly ValidationService $validationService,
    ) {}

    /**
     * @throws InvalidFileException
     * @throws ValidationFailedException
     */
    public function execute(StartImportRequest $request): StartImportResponse
    {
        // Create value objects
        $importTaskId = ImportTaskId::generate();
        $fileName = new FileName($request->fileName);
        $userId = UserId::from($request->userId);
        $templateId = TemplateId::from($request->templateId);
        $mappingStrategy = MappingStrategy::from($request->mappingStrategy);

        // Read file content
        if (!file_exists($request->filePath)) {
            throw new InvalidFileException('File not found');
        }

        $fileContent = new FileContent(file_get_contents($request->filePath));

        // Parse Excel file
        try {
            $parseResult = $this->excelParserService->parse($fileName, $fileContent);
            $headers = $parseResult['headers'];
            $rows = $parseResult['rows'];
        } catch (\Throwable $e) {
            throw new InvalidFileException('Failed to parse Excel file: ' . $e->getMessage());
        }

        // Create ImportTask entity
        $importTask = ImportTask::create(
            $importTaskId,
            $fileName,
            $userId,
            $templateId,
            $mappingStrategy,
        );

        // Start the import
        $importTask->start();

        // Validate and store rows
        $importedRows = [];
        $rowNumber = 2; // Skip header row (1)
        $failedValidations = [];

        foreach ($rows as $row) {
            // TODO: Get validation rules from template
            // For now, use empty rules
            $validationResult = $this->validationService->validate($row, []);

            $importedRow = ImportedRow::create(
                $importTask->id->value(),
                RowNumber::from($rowNumber),
                $row,
                $validationResult,
            );

            $importedRows[] = $importedRow;

            if (!$validationResult->isValid()) {
                $failedValidations[$rowNumber] = $validationResult->errors();
            }

            $rowNumber++;
        }

        // Check if there are validation failures
        if (!empty($failedValidations)) {
            $importTask->fail('Validation errors in rows: ' . implode(', ', array_keys($failedValidations)));
            $this->importTaskRepository->save($importTask);
            throw new ValidationFailedException('Import validation failed', $failedValidations);
        }

        // Save imported rows
        if (!empty($importedRows)) {
            $this->importedRowRepository->saveBatch($importedRows);
        }

        // Mark as succeeded
        $importTask->succeed(count($importedRows));
        $this->importTaskRepository->save($importTask);

        return new StartImportResponse(
            importTaskId: $importTask->id->value(),
            status: $importTask->status->toString(),
            fileName: $importTask->fileName->value(),
            createdAt: $importTask->createdAt->getTimestamp(),
        );
    }
}
