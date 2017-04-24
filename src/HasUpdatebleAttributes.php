<?php
/**
 * Created by PhpStorm.
 * User: danielcastro
 * Date: 4/14/17
 * Time: 1:51 PM
 */

namespace App\Traits;

use App\User;
use Illuminate\Support\Facades\DB;

trait HasUpdatebleAttributes
{


    protected $main_primary_key = 'id';

    protected $custom_primary_key = 'id_c';

    protected $custom_table;


    /**
     * @param $dbAttribute string Attribute name in the database
     * @param $value mixed New value
     * @param $where array|string|null Can be an array of conditions, just an ID or call from a Task model object
     * @return bool
     */
    public static function updateByAttribute( $dbAttribute, $value, $where )
    {
        return ( new static )->updateAttribute($dbAttribute, $value, $where);
    }

    /**
     * @param $dbAttribute string Attribute name in the database
     * @param $value mixed New value
     * @param $where array|string|null Can be an array of conditions, just an ID or call from a Task model object
     * @return bool
     */
    public function updateAttribute( $dbAttribute, $value, $where = null )
    {

        if( !$this->updateByAttributes($this->main_attributes, $dbAttribute, $value, $where) ) {
            if( !$this->updateByAttributes($this->custom_attributes, $dbAttribute, $value, $where, false) ) {
                return false;
            }
        }

        return true;
    }

    private function getCustomTableName()
    {
        return empty($this->custom_table) ? $this->main_table . '_cstm' : $this->custom_table;
    }

    public function updateByAttributes( $attributes, $dbAttribute, $value, $where = null, $useMainTable = true )
    {

        $key = $useMainTable ? $this->main_primary_key : $this->custom_primary_key;
        $tableName = $useMainTable ? $this->main_table : $this->getCustomTableName();

        if( in_array($dbAttribute, $attributes) ) {

            if( is_null($where) ) {

                $where = [
                    $key => $this->getAttribute($key),
                ];

            } else {

                if( !is_array($where) ) {
                    $where = [
                        $key => $where,
                    ];
                }
            }

            DB::table($tableName)
              ->where($where)
              ->update([ $dbAttribute => $value ]);

            return true;
        }

        return false;
    }


    public function create( array $attributes = [] )
    {
        $collectionAttributes = collect(array_keys($attributes));
        $collectionMainAttributes = collect($this->main_attributes);
        $collectionCustomAttributes = collect($this->custom_attributes);

        $mainTableAttributes = $collectionAttributes->intersect($collectionMainAttributes);
        $customTableAttributes = $collectionAttributes->intersect($collectionCustomAttributes);


        $mainTableValues = $this->getValuesFromAttributes($attributes, $mainTableAttributes);
        $customTableValues = $this->getValuesFromAttributes($attributes, $customTableAttributes);

        $this->createWithAttributes($this->main_table, $mainTableValues);

        $this->createWithAttributes($this->getCustomTableName(), $customTableValues);
    }

    /**
     * @param array $attributes
     * @return User
     */
    public function update( array $attributes = [] )
    {
        if (! $this->exists) {
            return false;
        }

        $collectionAttributes = collect(array_keys($attributes));
        $collectionMainAttributes = collect($this->main_attributes);
        $collectionCustomAttributes = collect($this->custom_attributes);

        $mainTableAttributes = $collectionAttributes->intersect($collectionMainAttributes);
        $customTableAttributes = $collectionAttributes->intersect($collectionCustomAttributes);


        $mainTableValues = $this->getValuesFromAttributes($attributes, $mainTableAttributes);
        $customTableValues = $this->getValuesFromAttributes($attributes, $customTableAttributes);

        $this->updateWithAttributes($this->main_table, $this->main_primary_key, $mainTableValues);

        $this->updateWithAttributes($this->getCustomTableName(), $this->custom_primary_key, $customTableValues);

    }

    private function getValuesFromAttributes( $attributesWithValues, $attributes )
    {
        $array = [];
        $attributes = $attributes->toArray(); //because it's a collection

        foreach( $attributesWithValues as $key => $attribute ) {
            if( in_array($key, $attributes) ) {
                $array[$key] = $attribute;
            }
        }

        return $array;

    }

    /**
     * @param $table
     * @param $key
     * @param $attributes
     * @return int
     */
    private function updateWithAttributes( $table, $key, $attributes )
    {
        if( count($attributes) ) {
            $where = [
                $key => $this->getAttribute($key),
            ];

            return DB::table($table)
                     ->where($where)
                     ->update($attributes);
        }
    }

    /**
     * @param $attributes
     * @return User|int|null
     */
    private function createWithAttributes( $tableName, $attributes )
    {
        if( count($attributes) ) {
            return DB::table($tableName)->insertGetId($attributes);
        } else {
            return null;
        }
    }

}

