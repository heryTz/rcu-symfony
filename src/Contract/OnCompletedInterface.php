<?php

namespace Herytz\RcuBundle\Contract;

interface OnCompletedInterface
{
  public function handle(OnCompletedData $data);
}