<?php

namespace Sophy\Providers;

use SophyDB\Connections\IDriver;
use SophyDB\Connections\PdoConn;

class DBDriverServiceProvider implements IServiceProvider {
    public function registerServices() {
        switch (config('database.driver', 'mysql')) {
            case 'mysql' || 'pgsql':
                singleton(IDriver::class, PdoConn::class);
                break;
            default:
                singleton(IDriver::class, PdoConn::class);
                break;
        }
    }
}
