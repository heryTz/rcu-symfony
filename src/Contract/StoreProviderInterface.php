<?php

namespace Herytz\RcuBundle\Contract;

interface StoreProviderInterface
{
  public function getItem(string $id): ?Upload;
  public function createItem(string $id, int $chunkCount): Upload;
  public function updateItem(string $id, Upload $update): Upload;
  public function removeItem(string $id): void;
}