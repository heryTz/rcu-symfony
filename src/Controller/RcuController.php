<?php

namespace Herytz\RcuBundle\Controller;

use Herytz\RcuBundle\Contract\OnCompletedData;
use Herytz\RcuBundle\Contract\OnCompletedInterface;
use Herytz\RcuBundle\Contract\StoreProviderInterface;
use Herytz\RcuBundle\Contract\UploadDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class RcuController extends AbstractController
{
  public function __construct(
    private string $tmpDir,
    private string $outputDir,
    private StoreProviderInterface $store,
    private ?OnCompletedInterface $onCompleted
  ) {
  }

  #[Route('%rcu.upload_status_path%', name: 'rcu_upload_status_path', methods: "GET")]
  public function uploadStatus(
    #[MapQueryParameter] string $fileId,
    #[MapQueryParameter] int $chunkCount,
  ): Response {
    $uploadInfo = $this->store->getItem($fileId);

    if ($uploadInfo === null) {
      $newUpload = $this->store->createItem($fileId, $chunkCount);
      // the last chunk is zero if the upload does not yet exist
      return $this->json(['lastChunk' => $newUpload->lastUploadedChunkNumber]);
    }

    // Some validation
    if ($uploadInfo->chunkCount !== $chunkCount) {
      $this->store->removeItem($fileId);
      $newUpload = $this->store->createItem($fileId, $chunkCount);
      return $this->json(['lastChunk' => $newUpload->lastUploadedChunkNumber]);
    }

    return $this->json(['lastChunk' => $uploadInfo->lastUploadedChunkNumber]);
  }

  #[Route('%rcu.upload_path%', name: 'rcu_upload_path', methods: "POST")]
  public function upload(
    #[MapRequestPayload] UploadDto $dto,
    Request $request,
    Filesystem $fs
  ): Response {
    $fs->mkdir([$this->tmpDir, $this->outputDir]);

    /** @var UploadedFile $file */
    $file = $request->files->get('file');
    if ($file === null) {
      return $this->json(['message' => 'No file uploaded'], 422);
    }

    $uploadInfo = $this->store->getItem($dto->fileId);
    if ($uploadInfo === null) {
      return $this->json(['message' => 'Invalid upload info ' . $dto->fileId], 422);
    }

    $chunkId = $dto->chunkNumber . '-' . $dto->fileId;
    try {
      $file->move($this->tmpDir, $chunkId);
    } catch (FileException $e) {
      return $this->json(['message' => 'Error uploading file'], 500);
    }

    $uploadInfo->chunkFilenames[] = $chunkId;
    $uploadInfo->lastUploadedChunkNumber = $dto->chunkNumber;
    $this->store->updateItem($dto->fileId, $uploadInfo);

    if ($uploadInfo->chunkCount > $dto->chunkNumber) {
      return $this->json(['message' => 'Chunk uploaded']);
    }

    $outputFile = Path::join($this->outputDir, $dto->originalFilename);
    $combinedFile = fopen($outputFile, 'w');

    foreach ($uploadInfo->chunkFilenames as $chunk) {
      $chunkPath = Path::join($this->tmpDir, $chunk);
      if (!file_exists($chunkPath)) {
        fclose($combinedFile);
        unlink($outputFile);
        $this->store->removeItem($dto->fileId);
        return $this->json(['message' => 'File corrupted'], 500);
      }

      fwrite($combinedFile, file_get_contents($chunkPath));
      unlink($chunkPath);
    }

    fclose($combinedFile);
    $this->store->removeItem($dto->fileId);

    if ($this->onCompleted) {
      $this->onCompleted->handle(new OnCompletedData($outputFile, $dto->fileId));
    }

    return $this->json([
      'message' => 'Upload complete',
      'outputFile' => $outputFile
    ]);
  }
}
