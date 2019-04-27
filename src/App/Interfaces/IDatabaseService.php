<?php
namespace App\Interfaces;

use \App\Record;

interface IDatabaseService {
  /**
   * Creates Table if table does not exist with set $tableName and $fields,
   * returns true/false depending on success of creation
   * @param String $tableName
   * @param Array $fields
   * @return Boolean 
   */
  public function createTable(String $tableName, Array $fields);

  /**
   * Adds desired values to desired table,
   * returns true/false depending on success of insertion
   * @param String $tableName
   * @param Record $record
   * @return Boolean
   */
  public function addRecordToTable(String $tableName, Record $record);

  /**
   * Reads whole table
   * @param String $table
   * @return Array
   */
  public function readTable(String $table);
}
