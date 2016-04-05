<?php
namespace Deployer\Database;

use Exception;

/**
 * Class Database
 * @package Deployer\Database
 */
class Database
{
    /**
     * @var string
     */
    public $suffix = '.flavor.json';

    /**
     * @var
     */
    private $dsn;
    /**
     * @var
     */
    private $username;
    /**
     * @var
     */
    private $password;

    /**
     * Database constructor.
     *
     * @param $dsn
     * @param $username
     * @param $password
     */
    public function __construct($dsn, $username, $password)
    {
        $this->dsn      = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param $stage
     *
     * @return Database
     * @throws Exception
     */
    public static function loadUsingStage($stage)
    {
        $filename = '../' . $stage . '.flavor.json';
        if (!file_exists($filename)) {
            throw new Exception($filename . ' is not found');
        }

        $config = json_decode(file_get_contents($filename))->components->db;

        return new Database($config->dsn, $config->username, $config->password);
    }

    public function dump()
    {
        $parsed = $this->parseDsn();

        $filename = sprintf('%s-%d.dump', $parsed->dbname, time());
        $command  = sprintf('mysqldump -u %s -p%s -h %s %s > %s',
            $this->username, $this->password, $parsed->host, $parsed->dbname, $filename);

        run($command);

        return $filename;
    }

    public function restore($dump)
    {
        $parsed = $this->parseDsn();

        $command = sprintf('mysql -u %s -p%s %s < %s',
            $this->username, $this->password, $parsed->dbname, $dump);

        return run($command);
    }

    private function parseDsn()
    {
        $pairs  = explode(';', substr($this->dsn, strpos($this->dsn, ':') + 1));
        $params = [];
        foreach ($pairs as $pair) {
            $params += parse_ini_string($pair);
        }

        return (object)$params;
    }
}