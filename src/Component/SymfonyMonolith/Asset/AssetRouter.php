<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Asset;

use InvalidArgumentException;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\MimeTypes;

final class AssetRouter
{
    /**
     * @var MimeTypes
     */
    private $mimeTypes;

    public function __construct(MimeTypes $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    public static function create(): self
    {
        return new self(new MimeTypes());
    }

    public function handle(KernelInterface $kernel, Request $request): ?Response
    {
        $kernel->boot();

        /** @var string $projectDir */
        $projectDir = $kernel->getContainer()->getParameter('kernel.project_dir');
        $projectDir = $this->normalizePath($projectDir);
        $ppublicDir = $this->resolvePublicDir($projectDir);

        $requestedAsset = realpath($this->concatenatePaths($ppublicDir, $request->getPathInfo()));

        // check if the provided path is within the public directory
        if (false === $requestedAsset || false === stripos($requestedAsset, $ppublicDir)) {
            return null;
        }

        if (file_exists($requestedAsset) && is_file($requestedAsset)) {
            $file = new SplFileInfo($requestedAsset);
            $mimeTypes = $this->mimeTypes->getMimeTypes($file->getExtension());

            return new BinaryFileResponse($file, 200, [
                'Content-Type' => reset($mimeTypes),
            ]);
        }

        return null;
    }

    private function resolvePublicDir(string $projectDir): string
    {
        $publicDir = 'public';

        $composerConfig = $this->getComposerContents($this->concatenatePaths($projectDir, 'composer.json'));
        if (null !== $composerConfig && isset($composerConfig['extra']['public-dir']) && is_string($composerConfig['extra']['public-dir'])) {
            $publicDir = $composerConfig['extra']['public-dir'];
        }

        return $this->concatenatePaths($projectDir, $this->normalizePath($publicDir));
    }

    /**
     * @return array<mixed>|null
     */
    private function getComposerContents(string $file): ?array
    {
        if (!file_exists($file)) {
            return null;
        }

        $contents = file_get_contents($file);
        if (false === $contents) {
            return null;
        }

        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Invalid composer definition. Expected decoded result to be an array.');
        }

        return $decoded;
    }

    private function normalizePath(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    private function concatenatePaths(string $path1, string $path2): string
    {
        return rtrim($path1, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path2, DIRECTORY_SEPARATOR);
    }
}
