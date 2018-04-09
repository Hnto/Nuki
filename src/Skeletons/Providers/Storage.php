<?php
namespace Nuki\Skeletons\Providers;

use Nuki\Skeletons\Handlers\StorageHandler;

interface Storage {
    public function __construct(StorageHandler $storageHandler);
}
