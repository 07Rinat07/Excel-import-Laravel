<?php

/**
 * @OA\Info(
 *     title="Excel Import/Export API",
 *     version="1.0.0",
 *     description="Comprehensive API for Excel file management with advanced features including smart mapping, validation, multi-sheet support, and formatted exports",
 *     @OA\Contact(
 *         name="API Support",
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with username and password to get the authentication token",
 *     name="Token based based authentication",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="api_key",
 * )
 */

namespace App\Http\Controllers\Api;

/**
 * @OA\Tag(
 *     name="Failed Rows Management",
 *     description="API endpoints for managing and correcting failed import rows"
 * )
 *
 * @OA\Get(
 *     path="/tasks/{task}/failed-rows",
 *     operationId="getFailedRows",
 *     tags={"Failed Rows Management"},
 *     summary="Get all failed rows for a task",
 *     description="Retrieve paginated list of failed rows from a specific import task",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="task",
 *         in="path",
 *         description="Task ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved failed rows",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="data", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="row", type="object"),
 *                 @OA\Property(property="errors", type="object"),
 *                 @OA\Property(property="corrected_data", type="object"),
 *                 @OA\Property(property="is_corrected", type="boolean"),
 *                 @OA\Property(property="message", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             )),
 *             @OA\Property(property="summary", type="object",
 *                 @OA\Property(property="total_failed", type="integer"),
 *                 @OA\Property(property="total_corrected", type="integer"),
 *                 @OA\Property(property="total_pending", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 *
 * @OA\Get(
 *     path="/failed-rows/{failedRow}",
 *     operationId="showFailedRow",
 *     tags={"Failed Rows Management"},
 *     summary="Get single failed row details",
 *     description="Retrieve detailed information about a specific failed row",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="failedRow",
 *         in="path",
 *         description="Failed Row ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Failed row details",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="original_data", type="object"),
 *                 @OA\Property(property="corrected_data", type="object", nullable=true),
 *                 @OA\Property(property="errors", type="object"),
 *                 @OA\Property(property="is_corrected", type="boolean"),
 *                 @OA\Property(property="message", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/failed-rows/{failedRow}",
 *     operationId="updateFailedRow",
 *     tags={"Failed Rows Management"},
 *     summary="Update a failed row with corrected data",
 *     description="Correct and validate a single failed row",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="failedRow",
 *         in="path",
 *         description="Failed Row ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="corrected_data", type="object", description="Corrected row data")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Row corrected successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation failed")
 * )
 *
 * @OA\Post(
 *     path="/tasks/{task}/failed-rows/bulk-update",
 *     operationId="bulkUpdateFailedRows",
 *     tags={"Failed Rows Management"},
 *     summary="Bulk update multiple failed rows",
 *     description="Correct and validate multiple failed rows at once",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="task",
 *         in="path",
 *         description="Task ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="rows", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="corrected_data", type="object")
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Bulk update completed",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="updated", type="integer"),
 *             @OA\Property(property="failed", type="array")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/tasks/{task}/failed-rows/reimport",
 *     operationId="reimportCorrectedRows",
 *     tags={"Failed Rows Management"},
 *     summary="Re-import all corrected rows",
 *     description="Queue corrected failed rows for re-import processing",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="task",
 *         in="path",
 *         description="Task ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Re-import queued successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="count", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=404, description="No corrected rows found")
 * )
 *
 * @OA\Delete(
 *     path="/failed-rows/{failedRow}",
 *     operationId="deleteFailedRow",
 *     tags={"Failed Rows Management"},
 *     summary="Delete a failed row",
 *     description="Mark a failed row as ignored by deleting it",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="failedRow",
 *         in="path",
 *         description="Failed Row ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Row deleted successfully"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="Sheet Selection & Mapping",
 *     description="API endpoints for multi-sheet handling and smart mapping"
 * )
 *
 * @OA\Get(
 *     path="/files/{file}/sheets",
 *     operationId="getAvailableSheets",
 *     tags={"Sheet Selection & Mapping"},
 *     summary="Get available sheets from file",
 *     description="Retrieve list of all sheets in an Excel file with statistics",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="file",
 *         in="path",
 *         description="File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Available sheets list",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="sheets", type="array", @OA\Items(
 *                     @OA\Property(property="index", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="total_rows", type="integer"),
 *                     @OA\Property(property="total_columns", type="integer"),
 *                     @OA\Property(property="has_data", type="boolean")
 *                 )),
 *                 @OA\Property(property="recommended_sheet_index", type="integer")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/files/{file}/sheets/headers",
 *     operationId="getSheetHeaders",
 *     tags={"Sheet Selection & Mapping"},
 *     summary="Get headers from a sheet",
 *     description="Retrieve column headers from a specific sheet",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="file",
 *         in="path",
 *         description="File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="sheet_index", type="integer", description="Sheet index (0-based)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Sheet headers retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="headers", type="array", @OA\Items(type="string")),
 *                 @OA\Property(property="column_count", type="integer")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/files/{file}/sheets/preview",
 *     operationId="getSheetPreview",
 *     tags={"Sheet Selection & Mapping"},
 *     summary="Get preview of sheet data",
 *     description="Retrieve sample data from a sheet for preview",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="file",
 *         in="path",
 *         description="File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="sheet_index", type="integer"),
 *             @OA\Property(property="rows_count", type="integer", default=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Preview data retrieved"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/files/{file}/sheets/select",
 *     operationId="selectSheetForImport",
 *     tags={"Sheet Selection & Mapping"},
 *     summary="Select sheet and initialize import",
 *     description="Select a sheet and perform smart mapping with template",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="file",
 *         in="path",
 *         description="File ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="sheet_index", type="integer"),
 *             @OA\Property(property="template_id", type="integer"),
 *             @OA\Property(property="task_name", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Sheet selected and mapped",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="task_id", type="integer"),
 *                 @OA\Property(property="selected_sheet", type="object"),
 *                 @OA\Property(property="mapping_suggestion", type="object")
 *             )
 *         )
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="Enhanced Export",
 *     description="API endpoints for formatted exports with styling and charts"
 * )
 *
 * @OA\Get(
 *     path="/projects/{project}/export/formatted",
 *     operationId="exportFormattedData",
 *     tags={"Enhanced Export"},
 *     summary="Export project data with formatting",
 *     description="Export project data as formatted Excel file with headers, styling, and summary",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="project",
 *         in="path",
 *         description="Project ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Formatted Excel file",
 *         @OA\JsonContent(type="string", format="binary")
 *     )
 * )
 *
 * @OA\Get(
 *     path="/projects/{project}/export/report",
 *     operationId="exportReport",
 *     tags={"Enhanced Export"},
 *     summary="Export as professional report",
 *     description="Export project data as professional report format with title and metadata",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="project",
 *         in="path",
 *         description="Project ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Report Excel file"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/projects/{project}/export/validated",
 *     operationId="exportWithValidation",
 *     tags={"Enhanced Export"},
 *     summary="Export with data validation",
 *     description="Export project data with data validation rules based on template",
 *     security={{"api_key":{}}},
 *     @OA\Parameter(
 *         name="project",
 *         in="path",
 *         description="Project ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Validated Excel file"
 *     )
 * )
 */
