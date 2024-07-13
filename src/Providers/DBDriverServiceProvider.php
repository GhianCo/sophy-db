<?php

namespace Sophy\Providers;

use SophyDB\Connections\IDBDriver;
use SophyDB\Connections\PdoDriver;

class DBDriverServiceProvider implements IServiceProvider {
    public function registerServices() {
        switch (config('database.driver', 'mysql')) {
            case 'mysql' || 'pgsql':
                singleton(IDBDriver::class, PdoDriver::class);
                break;
            default:
                singleton(IDBDriver::class, PdoDriver::class);
                break;
        }
    }
}
