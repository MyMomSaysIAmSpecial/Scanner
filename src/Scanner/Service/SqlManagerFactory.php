<?php


namespace Scanner\Service;

use Illuminate\Database\Capsule\Manager;

class SqlManagerFactory
{
    public function getSqlManager(Config $config)
    {
        $database = $config->get('database');
        $manager = new Manager();
        $manager->addConnection(
            [
                'driver'    => 'mysql',
                'host'      => $database['host'],
                'database'  => $database['dbname'],
                'username'  => $database['user'],
                'password'  => $database['pass'],
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        );

        return $manager;
    }
}