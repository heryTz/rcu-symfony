<?php

namespace Herytz\RcuBundle\Contract;

class Upload
{
  /**
   * @param string $id
   * @param int $chunkCount
   * @param int $lastUploadedChunkNumber
   * @param string[] $chunkFilenames
   */
  public function __construct(
    public string $id,
    public int $chunkCount,
    public int $lastUploadedChunkNumber,
    public array $chunkFilenames
  ) {
  }
}