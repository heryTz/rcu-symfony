<?php

namespace Herytz\RcuBundle\StoreProvider;

use Herytz\RcuBundle\Contract\StoreProviderInterface;
use Herytz\RcuBundle\Contract\Upload;

class JsonStoreProvider implements StoreProviderInterface
{
  public function __construct(
    private string $storeDir
  ) {
    if (!file_exists($storeDir)) {
      mkdir($storeDir, recursive: true);
    }
  }

  public function getItem(string $id): ?Upload
  {
    if (!file_exists($this->storeDir)) {
      mkdir($this->storeDir, recursive: true);
    }

    $filename = $this->storeDir . '/' . $id . '.json';
    if (!file_exists($filename)) {
      return null;
    }

    $data = json_decode(file_get_contents($filename), true);
    return new Upload(
      $data['id'],
      $data['chunkCount'],
      $data['lastUploadedChunkNumber'],
      $data['chunkFilenames']
    );
  }

  public function createItem(string $id, int $chunkCount): Upload
  {
    $upload = new Upload($id, $chunkCount, 0, []);
    $this->updateItem($id, $upload);
    return $upload;
  }

  public function updateItem(string $id, Upload $update): Upload
  {
    $filename = $this->storeDir . '/' . $id . '.json';
    file_put_contents($filename, json_encode($update));
    return $update;
  }

  public function removeItem(string $id): void
  {
    $filename = $this->storeDir . '/' . $id . '.json';
    unlink($filename);
  }
}
