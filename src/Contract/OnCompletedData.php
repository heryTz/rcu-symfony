<?php

namespace Herytz\RcuBundle\Contract;

class OnCompletedData
{
  public function __construct(
    public string $outputFile,
    public string $fileId
  ) {
  }
}