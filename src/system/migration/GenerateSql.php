<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\migration;

use Ozz\Core\system\cli\CliUtils;
use Ozz\Core\system\Ozz_CLI_Connection;

class GenerateSql {

  use Ozz_CLI_Connection;

  private $validDataTypes;
  private $validConstraint;
  private $finalSchemas = [];
  private $cli_utils;
  private $conn;

  function __construct(){
    $this->conn = $this->mysql();
    $this->cli_utils = new CliUtils;

    // Set Valid Schema commands
    $this->validDataTypes = [
      'int'                => 'INT',
      'txt'                => 'TEXT',
      'text'               => 'TEXT',
      'string'             => 'VARCHAR',
      'str'                => 'VARCHAR',
      'var'                => 'VARCHAR',
      'varchar'            => 'VARCHAR',

      'char'               => 'CHAR',
      'tinytext'           => 'TINYTEXT',
      'tinytxt'            => 'TINYTEXT',
      'ttext'              => 'TINYTEXT',
      'ttxt'               => 'TINYTEXT',
      'mediumtext'         => 'MEDIUMTEXT',
      'mediumtxt'          => 'MEDIUMTEXT',
      'mtext'              => 'MEDIUMTEXT',
      'mtxt'               => 'MEDIUMTEXT',
      'longtext'           => 'LONGTEXT',
      'ltext'              => 'LONGTEXT',
      'ltxt'               => 'LONGTEXT',
      'longtxt'            => 'LONGTEXT',
      'bigtext'            => 'LONGTEXT',
      'bigtxt'             => 'LONGTEXT',
      'btext'              => 'LONGTEXT',
      'btxt'               => 'LONGTEXT',

      'tinyblob'           => 'TINYBLOB',
      'tblob'              => 'TINYBLOB',
      'blob'               => 'BLOB',
      'mediumblob'         => 'MEDIUMBLOB',
      'mblob'              => 'MEDIUMBLOB',
      'longblob'           => 'LONGBLOB',
      'lblob'              => 'LONGBLOB',
      'set'                => 'SET',
      'binary'             => 'BINARY',
      'varbinary'          => 'VARBINARY',
      'enum'               => 'ENUM',

      'smallint'           => 'SMALLINT',
      'sint'               => 'SMALLINT',
      'tinyint'            => 'TINYINT',
      'tint'               => 'TINYINT',
      'mediumint'          => 'MEDIUMINT',
      'mint'               => 'MEDIUMINT',
      'bigint'             => 'BIGINT',
      'bint'               => 'BIGINT',
      'dec'                => 'DECIMAL',
      'decimal'            => 'DECIMAL',
      'float'              => 'FLOAT',
      'double'             => 'DOUBLE',
      'real'               => 'REAL',
      'bit'                => 'BIT',
      'bool'               => 'BOOLEAN',
      'serial'             => 'SERIAL',

      'date'               => 'DATE',
      'datetime'           => 'DATETIME',
      'dt'                 => 'DATETIME',
      'timestamp'          => 'TIMESTAMP',
      'ts'                 => 'TIMESTAMP',
      'time'               => 'TIME',
      'tm'                 => 'TIME',
      'year'               => 'YEAR',
      'yr'                 => 'YEAR',

      'ai'                 => 'AUTO_INCREMENT',
      'auto_increment'     => 'AUTO_INCREMENT',
      'currenttimestamp'   => 'CURRENT_TIMESTAMP',
      'ctimestamp'         => 'CURRENT_TIMESTAMP',
      'ctime'              => 'CURRENT_TIMESTAMP',
      'nn'                 => 'NOT NULL',
      'notnull'            => 'NOT NULL',
      'null'               => 'NULL',
      'nul'                => 'NULL',
      'unique'             => 'UNIQUE',
      'unq'                => 'UNIQUE',
      'default'            => 'DEFAULT',
      'dflt'               => 'DEFAULT',

      'geometry'           => 'GEOMETRY',
      'geo'                => 'GEOMETRY',
      'point'              => 'POINT',
      'linestring'         => 'LINESTRING',
      'linestr'            => 'LINESTRING',
      'polygon'            => 'POLYGON',
      'mpoint'             => 'MULTIPOINT',
      'multipoint'         => 'MULTIPOINT',
      'multilinestr'       => 'MULTILINESTRING',
      'multilinestring'    => 'MULTILINESTRING',
      'multipolygon'       => 'MULTIPOLYGON',
      'mpolygon'           => 'MULTIPOLYGON',
      'geocol'             => 'GEOMETRYCOLLECTION',
      'geometrycol'        => 'GEOMETRYCOLLECTION',
      'geometrycollection' => 'GEOMETRYCOLLECTION',
      'rename'             => 'RENAME COLUMN',
      'change'             => 'RENAME COLUMN'
    ];

    $this->validConstraint = [
      'primary'            => 'PRIMARY KEY',
      'primarykey'         => 'PRIMARY KEY',
      'prim'               => 'PRIMARY KEY',
      'foreign'            => 'FOREIGN KEY',
      'foreignkey'         => 'FOREIGN KEY',
      'frn'                => 'FOREIGN KEY',
      'frnkey'             => 'FOREIGN KEY',
      'index'              => 'INDEX',
      'check'              => 'CHECK',
    ];
  }

  /**
   * Convert developer txt to SQL for CREATE
   */
  protected function generateCreateSql($baseData=[]){
    // Generate Working SQL Here
    if(empty($baseData)){
      $this->cli_utils->console_return("No migrations found", 'white', 'red', 1, true);
      $this->cli_utils->console_colored("For create your migration file run [ php ozz c:migration migration_name ]", 'cyan');
      $this->cli_utils->console_colored("For more info [ php ozz -h ]", 'cyan');
    } else {
      foreach ($baseData as $key => $value) {
        $fields = [];
        $constr = []; // Constraint (Single for Table)

        foreach ($value as $k => $v) {
          $dtypSQL = [];
          foreach ($v as $dtyp) {
            if(strpos($dtyp, ':') !== false){
              $dtyp = explode(':', $dtyp);

              if(array_key_exists($dtyp[0], $this->validDataTypes)){
                $dtypSQL[] = $this->validDataTypes[$dtyp[0]].'('.$dtyp[1].')';
              } else {
                switch ($dtyp[0]) {
                  case 'check':
                    $constr['CHECK'] = $dtyp[1];
                    break;
                }
              }
            } elseif(array_key_exists($dtyp, $this->validDataTypes)){
              switch ($this->validDataTypes[$dtyp]) {
                case 'VARCHAR':
                  $dtypSQL[] = $this->validDataTypes[$dtyp].'(255)';
                  break;

                default:
                  $dtypSQL[] = $this->validDataTypes[$dtyp];
                  break;
              }
            } else {
            // Single values for each table
              switch ($dtyp) {
                case 'primary':
                case 'primarykey':
                case 'prim':
                  $constr['PRIMARY KEY'] = $k;
                  break;

                case 'foreign':
                case 'foreignkey':
                case 'frn':
                case 'frnkey':
                  $constr['FOREIGN KEY'] = $k;
                  break;

                case 'index':
                  $constr['INDEX'] = $k;
                  break;
              }
            }
          }

          $fields[$k] = $dtypSQL;
        }

        $this->finalSchemas[$key]['prop'] = $fields;
        $this->finalSchemas[$key]['const'] = $constr;

        // Set Dropable items and remove from inner array
        if(isset($baseData[$key]['drop']) && !empty($baseData[$key]['drop'])){
          $this->finalSchemas[$key]['drop'] = $baseData[$key]['drop'];
          unset($this->finalSchemas[$key]['prop']['drop']);
        }
      }

      return $this->final_TableCreation_SQL();
    }
  }

  /**
   * Get all SQL for ALTER
   */
  protected function generateAlterSql($baseData=[]){
    if(!empty($baseData)){
      $alterData = [];
      foreach ($baseData as $table => $value) {
        $addCols = $this->generateAlterSets($value['add']); // Add new columns
        $updateCols = $this->generateAlterSets($value['update']); // Update existing columns

        $alterData[$table]['add'] = $addCols;
        $alterData[$table]['update'] = $updateCols;
        $alterData[$table]['drop'] = $value['drop'];
        $alterData[$table]['const'] = [];

        if(isset($addCols['const']) && !empty($updateCols['const'])){
          $alterData[$table]['const'] = array_merge($addCols['const'], $updateCols['const']);
        } elseif(isset($addCols['const'])){
          $alterData[$table]['const'] = $addCols['const'];
        } elseif(isset($updateCols['const'])){
          $alterData[$table]['const'] = $updateCols['const'];
        }

        // Unset Const from Add/Update inside
        if(isset($alterData[$table]['add']['const'])){
          unset($alterData[$table]['add']['const']);
        }
        if(isset($alterData[$table]['update']['const'])){
          unset($alterData[$table]['update']['const']);
        }
      }

      $this->finalSchemas = $alterData;
      return $this->final_TableUpdating_SQL();
    }
  }

  /**
   * Convert developer txt to SQL for ALTER
   */
  private function generateAlterSets($data){
    $returnData = [];
    if(!empty($data)){
      foreach ($data as $ky => $v) {

        // Check for real values
        foreach ($v as $k => $df) {
          if(strpos($df, ':') !== false){
            $dtyp = explode(':', $df);
            if(array_key_exists($dtyp[0], $this->validDataTypes)){
              if($this->validDataTypes[$dtyp[0]] == 'RENAME COLUMN'){
                $returnData[$ky][$k]['type'] = 'change';
                $returnData[$ky][$k]['context'] = $this->validDataTypes[$dtyp[0]].' '.$ky.' TO '.$dtyp[1];
              } else {
                $returnData[$ky][$k] = $this->validDataTypes[$dtyp[0]].'('.$dtyp[1].')';
              }
            }
          } elseif(array_key_exists($df, $this->validDataTypes)){
            switch ($this->validDataTypes[$df]) {
              case 'VARCHAR':
                $returnData[$ky][$k] = $this->validDataTypes[$df].'(255)';
                break;

              default:
                $returnData[$ky][$k] = $this->validDataTypes[$df];
                break;
            }
          } else {
            // Single values for each table
            $returnData['const'] = $this->setConstr($df, $ky);
          }
        }
      }
    }
    return $returnData;
  }

  // Set Constr
  private function setConstr($df, $ky){
    $constr = [];
    switch ($df) {
      case 'primary':
      case 'primarykey':
      case 'prim':
        $constr['PRIMARY KEY'] = $ky;
        break;

      case 'foreign':
      case 'foreignkey':
      case 'frn':
      case 'frnkey':
        $constr['FOREIGN KEY'] = $ky;
        break;

      case 'index':
        $constr['INDEX'] = $ky;
        break;
      }
      return $constr;
  }

  /**
   * All Tables Creation SQL
   */
  private function final_TableCreation_SQL(){
    $generateSql = $this->finalSchemas;

    if(isset($generateSql) && !empty($generateSql)){
      // Create Tables
      $allTablesSQL = [];
      foreach ($generateSql as $key => $value) {

        $comma = '';
        $sql_fields = "";
        $lastProp = array_key_last($value['prop']);
        foreach ($value['prop'] as $k => $v) {
          $sql_fields .= $k == $lastProp ? $k.' '.implode(' ', $v) : $k.' '.implode(' ', $v).', ';
        }

        $constr = "";
        if(!empty($value['const'])){
          $lastConst = array_key_last($value['const']);
          foreach ($value['const'] as $k => $v) {
            $constr .= $k == $lastConst ? "$k ($v) " : "$k ($v), ";
          }
          $comma = ',';
        }

        $allTablesSQL[$key] = 'CREATE TABLE IF NOT EXISTS '.$key.' ('.$sql_fields.$comma.' '.$constr.') ENGINE='.CONFIG['DB_DEFAULT_ENGINE'].' DEFAULT CHARSET='.CONFIG['DB_DEFAULT_CHARSET'].' COLLATE '.CONFIG['DB_DEFAULT_COLLATION'].';';
      }
      return $allTablesSQL;
    }
    
  }

  /**
   * All Tables Updating SQL
   */
  private function final_TableUpdating_SQL(){
    $generateSql = $this->finalSchemas;
    if(isset($generateSql) && !empty($generateSql)){
      
      $altTableSQL = [];// All Alter SQLs
      foreach ($generateSql as $table => $value) {

        $allSQL = [];
        $allSQL['addColumns'] = [];
        $allSQL['updateColumns'] = [];
        $allSQL['dropColumns'] = [];
        $allSQL['tableConsts'] = [];

        // Add New Columns SQL
        $addColumns = '';
        if(isset($value['add']) && !empty($value['add'])){
          $addLast = array_key_last($value['add']);
          foreach ($value['add'] as $key => $val) {
            $cm = $key == $addLast ? '' : ', ';
            $addColumns .= 'ADD COLUMN '.$key .' '. implode(' ', $val).$cm;
          }
          $allSQL['addColumns'] = 'SET sql_notes = 0; ALTER TABLE '.$table.' '.$addColumns;
        }

        // Edit Existing Columns
        $editCols = '';
        if(isset($value['update']) && !empty($value['update'])){
          $editLast = array_key_last($value['update']);
          foreach ($value['update'] as $key => $val) {
            $cm = $key == $editLast ? '' : ', ';

            if(is_array($val[0])){
              // Rename/Change
              $editCols .= $val[0]['context'].$cm;
            }
            else{
              $editCols .= 'MODIFY COLUMN '.$key .' '. implode(' ', $val).$cm;
            }
          }
          $allSQL['updateColumns'] = 'SET sql_notes = 0;  ALTER TABLE '.$table.' '.$editCols;
        }

        // Drop Columns
        $dropCols = '';
        if(isset($value['drop']) && !empty($value['drop'])){
          $dropLast = array_key_last($value['drop']);
          foreach ($value['drop'] as $key => $val) {
            $cm = $key == $dropLast ? '' : ', ';
            $dropCols .= 'DROP COLUMN '.$val.$cm;
          }
          $allSQL['dropColumns'] = 'SET sql_notes = 0;  ALTER TABLE '.$table.' '.$dropCols;
        }

        // Const Table
        $constCols = '';
        if(isset($value['const']) && !empty($value['const'])){
          $constLast = array_key_last($value['const']);

          foreach ($value['const'] as $key => $val) {
            $cm = $key == $constLast ? '' : ', ';
            $key = (strpos($key, 'PRIMARY') !== false) ? 'DROP PRIMARY KEY, ADD '.$key : $key;
            $constCols .= $key.'('.$val.')'.$cm;
          }

          $allSQL['tableConsts'] = 'ALTER TABLE '.$table.' '.$constCols;
        }

        $altTableSQL[$table] = $allSQL;
      }

      return $altTableSQL;
    }
  }

}