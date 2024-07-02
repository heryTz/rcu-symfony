<?php

namespace Herytz\RcuBundle\Contract;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


class UploadDto
{
  public function __construct(
    #[Assert\NotBlank(normalizer: 'trim')]
    public readonly string $fileId,

    #[Assert\NotBlank(normalizer: 'trim')]
    public readonly int $chunkNumber,

    #[Assert\NotBlank(normalizer: 'trim')]
    public readonly string $originalFilename,

    #[Assert\Positive]
    public readonly int $chunkCount,

    #[Assert\NotBlank(normalizer: 'trim')]
    public readonly int $chunkSize,

    #[Assert\NotBlank(normalizer: 'trim')]
    public readonly int $fileSize
  ) {
  }
}
