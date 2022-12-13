<?php

namespace Adata\HealthChecker\Helpers;

use Adata\HealthChecker\Entities\HealthEntity;
use Illuminate\Support\Facades\DB;

/**
 * DatabaseHelper class
 */
class DatabaseHelper
{
    /**
     * Check connection to database
     *
     * @param string $connection
     *
     * @return bool
     */
    public function checkConnection(string $connection): bool
    {
        try {
            $check = DB::connection($connection)
                       ->getPdo()
                       ->getAttribute(\PDO::ATTR_CONNECTION_STATUS);

            if ($check === HealthEntity::DB_SUCCESS_CONN_MESSAGE) {
                return true;
            }
        } catch (\Exception $exception) {
            return false;
        } catch (\PDOException $exception) {
            return false;
        }

        return false;
    }
}