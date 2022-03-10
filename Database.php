<?php

namespace NoxxPHP\Core;

use PDO;

class Database
{
    public \PDO $pdo;

    /**
     * set up db connection using PDO
     */
    public function __construct(array $config)
    {
        $dsn= $config['dsn'];
        $username= $config['username'];
        $password= $config['password'];
        $this->pdo= new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * run migration files in migration folder
     */
    public function runMigrations()
    {
        $this->createMigrationsTable();
        $applied= $this->getAppliedMigrations();

        $files= scandir(Application::$ROOT_DIR.'/migrations');
        $toApply= array_diff($files, $applied);

        $newMigrations= [];

        foreach($toApply as $migration){
            if($migration == '.' || $migration == '..'){
                continue;
            }

            require_once Application::$ROOT_DIR.'/migrations/'.$migration;
            $className= pathinfo($migration, PATHINFO_FILENAME);
            $instance= new $className();

            $this->log('Applying Migration '. $migration);
            $instance->up();
            $this->log('Applied Migration '. $migration);
            $newMigrations[]= $migration;
        }

        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else $this->log('Nothing to migrate');
    }

    /**
     * create table to track our migrations
     */
    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;");
    }

    /**
     * get already applied migrations
     */
    public function getAppliedMigrations()
    {
        $statement= $this->pdo->prepare("SELECT migration from migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * save new migrations to migrations table
     */
    public function saveMigrations(array $migrations)
    {
        $str= implode(',', array_map(fn($m)=>"('$m')", $migrations));
        $statement= $this->pdo->prepare("INSERT INTO migrations (migration) VALUES
            $str"
        );
        $statement->execute();
    }

    protected function log($message)
    {
        echo '['. date('Y-m-d H:i:s') .'] - '. $message. PHP_EOL;
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }
}