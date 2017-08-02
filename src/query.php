<?php
require_once 'rows.php';
require_once 'fields.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of result
 *
 * @author Banoosh
 */

class query {
    //put your code here
    
    private $connect,$sql, $Params, $BindNames,$result,$meta,$error=false;
    private $data = array();
    
    public function __construct($connect, $sql, $arrParams, $arrBindNames=false) {     
        $this->connect= $connect;
        $this->sql = $sql;
        $this->Params = $arrParams;
        $this->BindNames = $arrBindNames;
        $this->query();
    }
    
    
    
    public function __get($name) {
             
            if (!isset($this->data[$name]))
            {
              $this->data[$name] = $this->$name();  
            }            
            return $this->data[$name];
    }
    
    
    /**
    Free Mysqli result object from data
     **/
    public function free()
    {
        if ($this->meta) { 
          $this->meta->close();  
          $this->meta = null;
        }
        if ($this->result){
          $this->result->free_result(); 
          $this->result->close(); 
          $this->result = null;
        }
    }
    
    /**
    To Get SQL Text Query Throgh $mysqli->sql outside class
     **/
    protected function sql()
    {
        return $this->sql;    
    }
    
    /**
    To Get SQL Query Params Throgh $mysqli->Params outside class
     **/
    protected function Params()
    {
        return $this->Params; 
    }
   
    /**
    To Get Fields New Names Array Throgh $mysqli->BindNames outside class
     **/
    protected function BindNames()
    {
        return $this->BindNames; 
    }
    
    
    
    /**
    To Get Fields Array Throgh $mysqli->fields outside class
    **/    
    protected function rows()
    {
        if (!isset($this->data['fields']))
            {
                 $this->data['fields'] = $this->fields();
            }
        $fields = $this->data['fields']->all;
        $row = array(); 
        $rows = array(); 

           if ($this->meta) {                        
               $params = array(); 
               foreach ($fields AS $field) { 
                  $params[] = &$row[$field['name']]; 
               } 
                     
            $method = new ReflectionMethod('mysqli_stmt', 'bind_result'); 
            $method->invokeArgs($this->result, $params); 
            
                     while ($this->result->fetch()) { 
                         $rows[] = $this->row_data($row);
                     } 
           } 
           return new mysqli_rows($rows);
        }

     
    /**
    To Get Fields Array Throgh $mysqli->fields outside class
    **/
    protected function fields()
    {  
        $fields = array(); 
        if ($this->meta) { 
            while ($field = $this->meta->fetch_field()) { 
                $field->asname = $field->name;
                $fields[] = (array) $field; 
            } 
                
            $fields =  $this->add_custom_fields_names($fields);
                         
        }
            
            return new mysqli_fields($fields);
    }
    
    
    /**
    To Get Affected Rows No. Throgh $mysqli->affected_rows outside class
    **/
    protected function affected_rows()
    {
        if (!$this->meta) {            
            return $this->result->affected_rows; 
        }
        else {return 0; }
    }
        

    /**
    To Get Insert ID Throgh $mysqli->insert_id outside class
    **/
    protected function insert_id()
    {
        if (!$this->meta) {            
            return $this->result->insert_id; 
        }
        else {return 0; }
    }
    
   
    /**
    To Get Rows Count Throgh $mysqli->insert_id outside class
    **/
    protected function num_rows()
    {    
     return  $this->result->num_rows;
    }
    

     
        /**
	 * Parent (Main) function - Executes a query with security and can change columns name  (butter than query)
	 *
	 * @param string $sql SQL query
         * @param Array Query Parameters $arrParams('SELECT * FROM std WHERE x=?')    ? => Parameter
         * @param Array $arrBindNames Custom Columns Names
	 * @return resource Executed query
	 **/
        
    private function query() { 
              
        $this->result = $this->connect->prepare($this->sql); // Prepare Mysql Query through Mysqli
        if ($this->result) { 
            $this->bind_param();
            $this->result->execute(); 
            $this->result->store_result();
            $this->meta = $this->result->result_metadata(); //(Insert Or Update Query will return false) (SELECT Query will return Array)
        } 
        else 
        {
            $this->error = array();
            $this->error['exist'] = True;
            $this->error['errno'] = $this->connect->errno;
            $this->error['errde'] = $this->connect->error;
            throw new \Exception('Mysqli Error No.: '. $this->connect->errno.',   Details:  '. $this->connect->error.',   Query:  '.$this->sql ); 
        }                          
    } 

        
        
         /**
	 * Send Parameters Array to Mysqli Class mysqli_stmt::bind_param
	 *
	 * @param Prepar Mysqli Resault $query_result + $params Parameters array 
	 * @return true because it is to send parameters only
	 **/

        private function bind_param()
        {
            if (count($this->Params) > 1)
                 {
                $this->Params = $this->getRefArray($this->Params); // <-- Added due to changes since PHP 5.3 
                $method = new ReflectionMethod('mysqli_stmt', 'bind_param'); 
                $method->invokeArgs($this->result, $this->Params);    
                 }
             return true;

        }
        
        
       
        
        
        
        
                        
         /**
	 * Add custom fields names to fields object array 
	 *
	 * @param Prepar Mysqli $meta results , Custom fields names array 
	 * @return fields data object array
	 **/

        private function add_custom_fields_names($fields)
        {
          if ($this->BindNames && count($this->BindNames) == count($fields))
            {
               for ($i=0,$j=count($this->BindNames); $i<$j; $i++) 
                        { 
                            $fields[$i]['name'] = $this->BindNames[$i];    
                            $fields[$i]['cname'] = $this->BindNames[$i]; 
                        } 

            }
            return $fields;
        }


        
        
         
        /**
	 * Convert Refrenced Array to Value Array
	 *
	 * @param Array $row  from data() func
	 * @return array of Data (One Row)
	 **/

        private function row_data($row)
        {
            $row_data = array();
            foreach ($row as $key=>$value)
               {
                    $row_data[$key] = $value;
               }  
               
               return $row_data;
        }        
        

         /**
	 * Create Refrenced Array to solve problem with bind_param in mysqli
	 * Added due to changes since PHP 5.3 
	 * @param Array $a 
	 * @return resource Executed query
	 **/

        private function getRefArray($a) { 
            if (strnatcmp(phpversion(),'5.3')>=0) { 
              $keys = array_keys($a);
              $ret = array(); 
              foreach($keys as $key ) { 
               $ret[$key] = &$a[$key]; 
              } 
              return $ret; 
            } 
            return $a; 
        } 
        
        
        
        public function __destruct() {
            $this->free();
        }

}
