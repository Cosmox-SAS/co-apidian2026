<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * Get the configured storage disk instance.
     */
    public static function disk()
    {
        $driver = config('filesystems.default', 'local');
        return Storage::disk($driver);
    }

    /**
     * Check if the current storage is S3.
     */
    public static function isS3(): bool
    {
        return config('filesystems.default') === 's3';
    }

    /**
     * Put content to the configured storage disk.
     */
    public static function put(string $path, $content): bool
    {
        return static::disk()->put($path, $content);
    }

    /**
     * Get file content from the configured storage disk.
     */
    public static function get(string $path): ?string
    {
        return static::disk()->get($path);
    }

    /**
     * Check if a file exists on the configured storage disk.
     */
    public static function exists(string $path): bool
    {
        return static::disk()->exists($path);
    }

    /**
     * Delete a file from the configured storage disk.
     */
    public static function delete(string $path): bool
    {
        return static::disk()->delete($path);
    }

    /**
     * Create a directory on the configured storage disk (no-op for S3).
     */
    public static function makeDirectory(string $path): bool
    {
        if (static::isS3()) {
            return true; // S3 doesn't need explicit directory creation
        }
        return static::disk()->makeDirectory($path);
    }

    /**
     * Check if a directory/path exists on the configured storage disk.
     */
    public static function has(string $path): bool
    {
        if (static::isS3()) {
            return true; // S3 doesn't need explicit directory checking
        }
        return Storage::has($path);
    }

    /**
     * Upload a locally generated file (PDF, ZIP, etc.) to the configured storage disk.
     * For local disk, copies the file to the correct storage location.
     * For S3, uploads the content and optionally removes the local temp file.
     */
    public static function putLocalFile(string $storagePath, string $localFilePath, bool $deleteLocal = true): bool
    {
        if (!file_exists($localFilePath)) {
            return false;
        }

        $content = file_get_contents($localFilePath);

        if (static::isS3()) {
            $result = static::disk()->put($storagePath, $content);
            if ($deleteLocal) {
                @unlink($localFilePath);
            }
            return $result;
        }

        // For local disk, if the file is already in the right place, skip copy
        $targetPath = storage_path('app/' . $storagePath);
        if (realpath($localFilePath) !== realpath($targetPath)) {
            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            copy($localFilePath, $targetPath);
            if ($deleteLocal) {
                @unlink($localFilePath);
            }
        }

        return true;
    }

    /**
     * Get a local temp path for generating files (PDFs, ZIPs).
     * Always returns a writable local path, regardless of storage driver.
     */
    public static function tempPath(string $relativePath): string
    {
        if (static::isS3()) {
            $tempDir = sys_get_temp_dir() . '/apidian';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $subDir = dirname($relativePath);
            if ($subDir && $subDir !== '.') {
                $fullDir = $tempDir . '/' . $subDir;
                if (!is_dir($fullDir)) {
                    mkdir($fullDir, 0755, true);
                }
            }
            return $tempDir . '/' . $relativePath;
        }

        return storage_path('app/' . $relativePath);
    }

    /**
     * Get the real local storage path (for backwards compatibility during migration).
     * On local disk, returns the standard storage_path.
     * On S3, first checks if file exists locally, then tries S3.
     * TODO: Temporalmente prioriza archivo local sobre S3
     */
    public static function localPath(string $storagePath): string
    {
        // Siempre verificar primero si existe localmente
        $localFile = storage_path('app/' . $storagePath);
        if (file_exists($localFile)) {
            return $localFile;
        }

        if (static::isS3()) {
            $tempFile = static::tempPath($storagePath);
            // Solo intentar descargar de S3 si existe allí
            try {
                if (static::disk()->exists($storagePath)) {
                    $dir = dirname($tempFile);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    file_put_contents($tempFile, static::disk()->get($storagePath));
                    return $tempFile;
                }
            } catch (\Exception $e) {
                // Si falla S3, devolver ruta local aunque no exista
            }
            return $tempFile;
        }

        return $localFile;
    }

    /**
     * Download response from the configured storage disk.
     */
    public static function download(string $path, string $name = null)
    {
        if (static::isS3()) {
            $content = static::disk()->get($path);
            if ($content === null) {
                abort(404, 'File not found');
            }
            $filename = $name ?? basename($path);
            $mime = static::guessMimeType($filename);
            return response($content, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return static::disk()->download($path, $name);
    }

    /**
     * Inline response (view in browser) from the configured storage disk.
     */
    public static function inline(string $path, string $name = null)
    {
        $content = static::get($path);
        if ($content === null) {
            abort(404, 'File not found');
        }
        $filename = $name ?? basename($path);
        $mime = static::guessMimeType($filename);
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get file content as base64 encoded string.
     */
    public static function getBase64(string $path): ?string
    {
        $content = static::get($path);
        if ($content === null) {
            return null;
        }
        return base64_encode($content);
    }

    /**
     * Guess MIME type based on file extension.
     */
    protected static function guessMimeType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimes = [
            'pdf' => 'application/pdf',
            'xml' => 'application/xml',
            'zip' => 'application/zip',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        return $mimes[$ext] ?? 'application/octet-stream';
    }

    /**
     * Ensure a directory exists for the given relative path.
     * Works for both local and S3 modes.
     */
    public static function ensureDirectory(string $relativePath): void
    {
        if (static::isS3()) {
            $tempDir = static::tempPath($relativePath);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
        } else {
            $dir = storage_path('app/' . $relativePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Get base64 encoded content, trying local temp first (for recently generated files),
     * then falling back to the configured storage disk.
     */
    public static function getBase64Auto(string $relativePath): ?string
    {
        // Try local temp first (file may have just been generated)
        if (static::isS3()) {
            $tempFile = static::tempPath($relativePath);
            if (file_exists($tempFile)) {
                return base64_encode(file_get_contents($tempFile));
            }
        }
        return static::getBase64($relativePath);
    }

    /**
     * Upload a locally generated file to S3 if in S3 mode.
     * Does nothing in local mode since files are already in the right place.
     */
    public static function uploadIfS3(string $relativePath, string $localPath = null): void
    {
        if (!static::isS3()) {
            return;
        }
        $localPath = $localPath ?? static::tempPath($relativePath);
        if (file_exists($localPath)) {
            static::putLocalFile($relativePath, $localPath, false);
        }
    }

    /**
     * Upload multiple local files to S3 after DIAN processing.
     * Accepts an array of relative paths.
     */
    public static function uploadBatchIfS3(array $relativePaths): void
    {
        if (!static::isS3()) {
            return;
        }
        foreach ($relativePaths as $relativePath) {
            static::uploadIfS3($relativePath);
        }
    }

    /**
     * Check if a file exists in the local temp storage.
     */
    public static function existsLocal(string $relativePath): bool
    {
        return file_exists(static::tempPath($relativePath));
    }

    /**
     * Get file contents from local temp or S3, prioritizing local.
     * Useful for reading XML files that may be in either location.
     */
    public static function getAutoLocal(string $relativePath): ?string
    {
        $localPath = static::tempPath($relativePath);
        if (file_exists($localPath)) {
            return file_get_contents($localPath);
        }
        if (static::isS3() && static::exists($relativePath)) {
            return static::get($relativePath);
        }
        return null;
    }

    // =========================================================================
    // MÉTODOS "AUTO" PARA MIGRACIÓN LOCAL -> S3
    // Buscan primero en local storage, luego en S3 (para documentos antiguos)
    // =========================================================================

    /**
     * Check if file exists, first in local storage, then in S3.
     * Use for documents that may be in local (old) or S3 (new).
     */
    public static function existsAuto(string $relativePath): bool
    {
        // Primero buscar en storage local
        $localPath = storage_path('app/' . $relativePath);
        if (file_exists($localPath)) {
            return true;
        }
        // Si no está local y estamos en modo S3, buscar en S3
        if (static::isS3()) {
            try {
                return static::disk()->exists($relativePath);
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get file content, first from local storage, then from S3.
     * Use for documents that may be in local (old) or S3 (new).
     */
    public static function getAuto(string $relativePath): ?string
    {
        // Primero buscar en storage local
        $localPath = storage_path('app/' . $relativePath);
        if (file_exists($localPath)) {
            return file_get_contents($localPath);
        }
        // Luego buscar en temp (archivos recién generados)
        if (static::isS3()) {
            $tempPath = static::tempPath($relativePath);
            if (file_exists($tempPath)) {
                return file_get_contents($tempPath);
            }
            // Finalmente buscar en S3
            try {
                if (static::disk()->exists($relativePath)) {
                    return static::disk()->get($relativePath);
                }
            } catch (\Exception $e) {
                // Error de conexión S3
            }
        }
        return null;
    }

    /**
     * Get base64 content, first from local storage, then from S3.
     * Use for documents that may be in local (old) or S3 (new).
     */
    public static function getBase64AutoFallback(string $relativePath): ?string
    {
        $content = static::getAuto($relativePath);
        if ($content === null) {
            return null;
        }
        return base64_encode($content);
    }

    /**
     * Download file, first from local storage, then from S3.
     * Use for documents that may be in local (old) or S3 (new).
     */
    public static function downloadAuto(string $relativePath, string $name = null)
    {
        $content = static::getAuto($relativePath);
        if ($content === null) {
            abort(404, 'File not found');
        }
        $filename = $name ?? basename($relativePath);
        $mime = static::guessMimeType($filename);
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Inline (view in browser) file, first from local storage, then from S3.
     * Use for documents that may be in local (old) or S3 (new).
     */
    public static function inlineAuto(string $relativePath, string $name = null)
    {
        $content = static::getAuto($relativePath);
        if ($content === null) {
            abort(404, 'File not found');
        }
        $filename = $name ?? basename($relativePath);
        $mime = static::guessMimeType($filename);
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    // =========================================================================
    // MÉTODOS DE ALMACENAMIENTO LOCAL FORZADO
    // Para archivos que SIEMPRE deben estar localmente (certificados, logos)
    // =========================================================================

    /**
     * Put content to local storage ONLY (ignores S3 config).
     * Use for files that must be local: certificates, logos.
     */
    public static function putLocal(string $path, $content): bool
    {
        $fullPath = storage_path('app/' . $path);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($fullPath, $content) !== false;
    }

    /**
     * Get file content from local storage ONLY (ignores S3 config).
     * Use for files that must be local: certificates, logos.
     */
    public static function getLocal(string $path): ?string
    {
        $fullPath = storage_path('app/' . $path);
        if (!file_exists($fullPath)) {
            return null;
        }
        return file_get_contents($fullPath);
    }

    /**
     * Check if file exists in local storage ONLY (ignores S3 config).
     * Use for files that must be local: certificates, logos.
     */
    public static function existsLocalStorage(string $path): bool
    {
        return file_exists(storage_path('app/' . $path));
    }

    /**
     * Delete file from local storage ONLY (ignores S3 config).
     * Use for files that must be local: certificates, logos.
     */
    public static function deleteLocal(string $path): bool
    {
        $fullPath = storage_path('app/' . $path);
        if (file_exists($fullPath)) {
            return @unlink($fullPath);
        }
        return true;
    }

    /**
     * Get local storage path for a relative path (always returns local path).
     * Use for files that must be local: certificates, logos.
     */
    public static function localStoragePath(string $path): string
    {
        return storage_path('app/' . $path);
    }
}
