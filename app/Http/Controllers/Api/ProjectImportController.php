<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ImportStoreRequest;
use App\Application\Import\UseCases\StartImportUseCase;
use App\Application\Import\DTO\StartImportRequest as StartImportDTO;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\FileContent;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Domain\Import\ValueObjects\MappingStrategy;
use App\Domain\Import\Exceptions\InvalidFileException;
use App\Domain\Import\Exceptions\ValidationFailedException;
use App\Models\Type;
use Database\Seeders\TypesSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * Controller: ProjectImportController
 *
 * Thin layer for HTTP handling.
 * All business logic delegated to Use Cases.
 */
class ProjectImportController extends Controller
{
    public function __construct(
        private readonly StartImportUseCase $startImportUseCase,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Store a new import task.
     *
     * @param ImportStoreRequest $request
     * @return JsonResponse
     */
    public function store(ImportStoreRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            $typeId = $this->resolveTypeId($validatedData['type_id'] ?? null);

            if (! $typeId) {
                return response()->json([
                    'error' => 'Validation failed',
                    'message' => 'Types are not configured. Please create a type before importing.',
                ], 422);
            }

            // Log import start
            $this->logger->info('Import started', [
                'user_id' => $user->id,
                'file_name' => $validatedData['file']->getClientOriginalName(),
            ]);

            // Execute use case
            $response = $this->startImportUseCase->execute(
                new StartImportDTO(
                    fileName: new FileName($validatedData['file']->getClientOriginalName()),
                    fileContent: FileContent::fromFile($validatedData['file']),
                    userId: new UserId($user->id),
                    templateId: new TemplateId($typeId),
                    mappingStrategy: MappingStrategy::automatic(),
                )
            );

            return response()->json([
                'message' => 'Import task created successfully',
                'task_id' => $response->importTaskId->value(),
                'status' => 'pending',
                'created_at' => now()->toIso8601String(),
            ], 202);

        } catch (InvalidFileException $e) {
            $this->logger->warning('Invalid import file', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Invalid file format',
                'message' => $e->getMessage(),
            ], 422);

        } catch (ValidationFailedException $e) {
            $this->logger->warning('Import validation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            $this->logger->error('Import error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Failed to process import',
            ], 500);
        }
    }

    private function resolveTypeId(?int $typeId): ?int
    {
        if ($typeId) {
            return Type::query()->whereKey($typeId)->exists() ? $typeId : null;
        }

        if (! Type::query()->exists()) {
            app(TypesSeeder::class)->run();
        }

        return Type::query()->value('id');
    }
}
