<?php

declare(strict_types=1);

namespace App\Application\UploadFile\Request;

use App\Application\UploadFile\Response\UploadFileErrorResponse;
use App\Application\UploadFile\Response\UploadFileResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

class FileUploaderRequest
{
    /** octets */
    private int $maxFileSize = 5 * 1024 * 1024; // 5 MB par défaut
    /** allowed mime types */
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'application/pdf',
    ];

    public function __construct(
        private readonly SluggerInterface $slugger,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%kernel.project_dir%/public/uploads')]
        private readonly string $targetDirectory,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger,
        private readonly ?string $publicBaseUrl = null // ex: https://lead-forge.test/uploads
    ) {
        // Ensure target directory exists
        if (! $this->filesystem->exists($this->targetDirectory)) {
            $this->filesystem->mkdir($this->targetDirectory, 0o755);
        }
    }

    /**
     * @param string|null $existingFilename si fourni, le fichier existant sera supprimé
     * @param bool        $convertToWebp    si true et fichier image → conversion (optionnel, dépend d'une lib)
     *
     * @throws UploadException
     */
    public function upload(UploadedFile $file, ?string $existingFilename = null, bool $convertToWebp = false): UploadFileResponse
    {
        // 1) Basic validations (size + mime)
        $size = $file->getSize() ?? 0;
        if ($size <= 0) {
            $this->logger->warning('Upload: fichier vide ou taille inconnue.', ['clientName' => $file->getClientOriginalName()]);
        }

        if ($size > $this->maxFileSize) {
            UploadFileErrorResponse::fileTooLarge(
                [
                    'size' => $size,
                    'max' => $this->maxFileSize,
                ]
            );
        }

        $mimeType = $file->getClientMimeType() ?? MimeTypes::getDefault()->guessMimeType($file->getPathname()) ?? 'application/octet-stream';
        if (! in_array($mimeType, $this->allowedMimeTypes, true)) {
            UploadFileErrorResponse::invalidMimeType(
                [
                    'mimyType' => $mimeType,
                    'allowed' => $this->allowedMimeTypes,
                ]
            );
        }

        // 2) Generate safe filename with extension
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = (string) $this->slugger->slug($originalFilename);
        $uuid = Uuid::v4()->toRfc4122();
        $extension = $this->guessExtension($file, $mimeType);

        $newFilename = sprintf('%s-%s.%s', $safeBase, $uuid, $extension);

        // If caller passed an existing filename, attempt to delete it first (safe)
        if ($existingFilename) {
            $this->deleteFile($existingFilename);
        }

        // 3) Move file to target dir (atomic as possible)
        try {
            // move() already ensures temp -> final. Use unique filename to avoid collisions.
            $file->move($this->targetDirectory, $newFilename);
        } catch (\Throwable $e) {
            $this->logger->error('Upload: erreur lors du déplacement du fichier.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            UploadFileErrorResponse::cannotMove(
                [
                    'system' => $e->getMessage(),
                ]
            );
        }

        // Optional: convert to webp (only if requested and it's an image)
        if ($convertToWebp && str_starts_with($mimeType, 'image/')) {
            try {
                $this->convertToWebp($this->targetDirectory.DIRECTORY_SEPARATOR.$newFilename);
                // rename new filename to .webp if conversion replaces it
                $newFilename = preg_replace('/\.[^.]+$/', '.webp', $newFilename);
            } catch (\Throwable $e) {
                // Conversion failure should not bring down the upload — log and continue
                $this->logger->warning('Upload: conversion WebP échouée, conservation du fichier original.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 4) Build public URL if base provided
        $relativePath = 'uploads/'.$newFilename;
        $publicUrl = $this->publicBaseUrl ? rtrim($this->publicBaseUrl, '/').'/'.$newFilename : '';

        return UploadFileResponse::fromFile(
            $file->getClientOriginalName(),
            $newFilename,
            $mimeType,
            $size,
            $relativePath,
            $publicUrl
        );
    }

    /**
     * Delete a file from the uploads directory in a safe manner.
     */
    public function deleteFile(?string $filename = null): void
    {
        if (empty($filename)) {
            return;
        }

        // Protect against directory traversal
        $basename = basename($filename);
        $path = $this->targetDirectory.DIRECTORY_SEPARATOR.$basename;

        if ($this->filesystem->exists($path)) {
            try {
                $this->filesystem->remove($path);
            } catch (\Throwable $e) {
                $this->logger->error('Upload: erreur lors de la suppression de fichier.', [
                    'file' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Guess extension based on uploaded file and mime type.
     */
    private function guessExtension(UploadedFile $file, string $mimeType): string
    {
        // Prefer client extension if safe
        $clientExt = strtolower($file->getClientOriginalExtension() ?? '');
        if ($clientExt && preg_match('/^[a-z0-9]+$/', $clientExt)) {
            return $clientExt;
        }

        // Fallback using mime types mapping
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
        ];

        return $map[$mimeType] ?? 'bin';
    }

    /**
     * Optional: Convert image to WebP using GD or Imagick.
     * You can implement using intervention/image or native GD if available.
     *
     * @throws \RuntimeException on failure
     * @throws \ImagickException
     */
    private function convertToWebp(string $filepath): void
    {
        if (! extension_loaded('imagick') && ! extension_loaded('gd')) {
            throw new \RuntimeException('Neither Imagick nor GD available for image conversion to WebP.');
        }

        $pathInfo = pathinfo($filepath);
        $target = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';

        // Example using Imagick if available
        if (extension_loaded('imagick')) {
            $img = new \Imagick($filepath);
            $img->setImageFormat('webp');
            $img->setOption('webp:lossless', 'true');
            $img->writeImage($target);
            $img->clear();
            $img->destroy();
            // Optionally remove original
            $this->filesystem->remove($filepath);

            return;
        }

        // Fallback using GD (PHP 8.4+ might have imagewebp)
        $mime = mime_content_type($filepath);
        if ('image/png' === $mime) {
            $image = imagecreatefrompng($filepath);
        } elseif ('image/gif' === $mime) {
            $image = imagecreatefromgif($filepath);
        } else {
            $image = imagecreatefromjpeg($filepath);
        }

        if (false === $image) {
            throw new \RuntimeException('GD: impossible de créer une ressource image.');
        }

        // Quality: 80
        if (! imagewebp($image, $target, 80)) {
            imagedestroy($image);

            throw new \RuntimeException('GD: conversion WebP échouée.');
        }

        imagedestroy($image);
        // remove original file
        $this->filesystem->remove($filepath);
    }

    // setters for configuration (optional)
    public function setMaxFileSize(int $bytes): void
    {
        $this->maxFileSize = $bytes;
    }

    public function setAllowedMimeTypes(array $types): void
    {
        $this->allowedMimeTypes = $types;
    }
}
