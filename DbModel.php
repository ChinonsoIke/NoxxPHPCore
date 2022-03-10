<?php

namespace NoxxPHP\Core;

abstract class DbModel extends Model
{
    abstract public static function tableName() : string;

    abstract public function attributes() : array;

    /**
     * save record to db
     */
    public function save()
    {
        $tableName= $this->tableName();
        $attributes= $this->attributes();
        $params= array_map(fn($attr)=> ":$attr", $attributes);
        $statement= self::prepare("INSERT INTO $tableName (".implode(',', $attributes).")
            VALUES(".implode(',', $params).")");
        
        // echo '<pre>';var_dump($statement, $params, $attributes);echo '</pre>';exit;

        foreach($attributes as $attr){
            $statement->bindValue(":$attr", $this->{$attr});
        }

        $statement->execute();
        return true;
    }

    public static function findOne($where) : DbModel
    {
        $tableName= static::tableName();
        $attributes= array_keys($where);
        $sql= implode(" AND ", array_map(fn($attr)=>"$attr = :$attr", $attributes));
        $statement= self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach($where as $key=>$value){
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    /**
     * prepare sql statement for execution
     */
    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}