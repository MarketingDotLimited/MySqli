<?php
require_once 'query.php';
class mysqli_db
{
    private $connect; // Connection link identifier @var resource
    private $connect_param; //Connection  Parameters Object (host=>'', user=>'', pass=>'', name=>'', port=>'', socket=>'')
    private $query_obj = array();
        	
	/**
	 * Constructor; sets up variables and connect
	 *
	 * @param string $db_host Server
	 * @param string $db_user User Name
	 * @param string $db_pass Password
	 * @param string $db_name Database Name
	 * @param int $db_port Database Port
	 * @param string $db_socket Database Socket
	 * @return void
	 **/
	 public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port = 3306, $db_socket = "")
         {     
                $this->connect_param = (object) array('host'=>$db_host,'user'=>$db_user,'pass'=>$db_pass,'db'=>$db_name,'port'=>$db_port,'socket'=>$db_socket);  
                $this->connect = new mysqli("$db_host:$db_port". (!$db_socket ? '' : ":$db_socket"), $db_user, $db_pass, $db_name);


                if( $this->connect->connect_error ) 
                    {
                 throw new \Exception('Database connect failed: '  . $this->connect->connect_error);
                    }

                
                /* change character set to utf8 */
                if (!$this->connect->set_charset("utf8"))
                    {
                        throw new \Exception("Error loading character set utf8: ". $this->connect->connect_error ); 
                    }
                }
        
        
        
        
        public function query($sql, $arrParams =array(), $arrBindNames=false)
        {

            $sql_commands = array("SELECT", "INSERT", "UPDATE", "REPLACE");
            $sql_query_text_1 = substr(trim(strtoupper($sql)), 0, 6);            
            $sql_query_text_2 = substr(trim(strtoupper($sql)), 0, 7);
            if (in_array($sql_query_text_1,$sql_commands) || in_array($sql_query_text_2,$sql_commands)) {

               $result  = new query($this->connect, $sql, $arrParams, $arrBindNames);
            }
                              
            $this->query_obj[] = $result;
            return end ($this->query_obj);
        }
        
      
        // $table = (table name) or combine array contain (table name and row values data)
        // $data = (one row values data) or ( Multible rows )
        public function insert($table,$data = NULL)
        {
            $tables_array = array();
            $insert_result = array();
            // Combine all parameters in one array
            $combines = $this->combine($table,$data);
            
            if (!$combines)
            {
                return NULL;
            }

            foreach ($combines as $combine)
            {    
                $table_name = $combine['table'];
               if (!in_array($table_name, $tables_array))
               {
                $tables_array[] = $table_name;
               }
                foreach ($combine['rows'] as $row) 
                {     
                    $fields_names_array = array_keys($row);
                    $fields_names_sql = implode(",", $fields_names_array);
                    $count = count($fields_names_array);
                    $values_t = array();
                    for ($i=0;$i<$count;$i++)
                    {
                        $values_t[$i] = "?";
                    }
                    $values_sql = implode(",", $values_t); 
                   $sql = "INSERT INTO {$table_name} ({$fields_names_sql}) VALUES ({$values_sql})";
            
                    //Fetch all fields details (name,orgname,type,....)
                    $field_array =  $this->fields_list($table_name,$row);
                    //create parameters
                    $Params = $this->params($field_array,$row);                
                    $insert_result[$table_name][] = $this->query($sql, $Params);
                }            
            }
            
            if (count($tables_array)== 1)
            {
                if (count($insert_result[$tables_array[0]]) == 1) 
                {
                return $insert_result[$tables_array[0]][0];
                }
                else 
                {
                return $insert_result[$tables_array[0]];
                }
            }
            
            return $insert_result;
        }
        
        
        
        // $table = (table name) or combine array contain (table name and row values data)
        // $data = (one row values data) or ( Multible rows )
        public function update($table,$data = NULL,$statement=NULL)
        {
            $tables_array = array();
            $insert_result = array();
            // Combine all parameters in one array
            $combines = $this->combine($table,$data,$statement);
            if (!$combines)
            {
                return NULL;
            }

            foreach ($combines as $combine)
            {    
                
            }
        }

            
        
        
        // Combine all parameters in one array    
        private function combine($table,$data,$statement=NULL)
        {
            $combines = array();
            if (is_string($statement) && is_array($data) && is_string($table))
            {
                if ($this->IsArrayAllKeyString($data))
                {
                           $statement = array('id'=>$statement);
                           $data = array('values'=>$data,'statement'=>$statement);
                           $combines[] = array('table'=>$table,'rows'=>$data);
                }

                
            }
            elseif (is_array($data)  && is_string($table)) 
            {
                if ($this->IsArrayAllKeyString($data))
                {
                           $data = array($data);
                }
                $combines[] = array('table'=>$table,'rows'=>$data);
            }
            elseif (($data == NULL || trim($data) == "" || count($data) <= 0) && IsArrayAllKeyInt($table))
            {
               foreach ($table as $combine)
               {
                    if ($this->IsArrayAllKeyInt($combine))
                    {  
                       if ($this->IsArrayAllKeyString($combine[1]))
                       {
                           $combine[1] = array($combine[1]);
                       }

                       $combines[] = array('table'=>$combine[0],'rows'=>$combine[1]);
                    }
                    else 
                    {
                       if ($this->IsArrayAllKeyString($combine['rows']))
                       {
                           $combine['rows'] = array($combine['rows']);
                       }
                        $combines[] = $combine;
                    }
               }
            }
            else 
            {
                return NULL;
            }
    
            return $combines;
        }


        
       //Fetch all fields details by table name (name,orgname,type,....) 
       public function  fields_list($table)
       {
            $sql_s = "SELECT * FROM {$table} LIMIT 0";
            $select_fields  = new query($this->connect, $sql_s, array(), array());
            $fields = $select_fields->fields->all;
            // Free Memory from select fields query resaults
            $select_fields->free();
            return $fields;
       }
       
       
       //create parameters from $data array to use in prepare mysqli query
       private function params($fields,$data)
       {
                /*
                    Integer myqli fields type
                  -------------
                    BIT: 16
                    TINYINT: 1
                    BOOL: 1
                    SMALLINT: 2
                    MEDIUMINT: 9
                    INTEGER: 3
                    BIGINT: 8
                    SERIAL: 8
                    
                    Double myqli fields type
                  -------------
                    FLOAT: 4
                    DOUBLE: 5
                    DECIMAL: 246
                    NUMERIC: 246
                    FIXED: 246
                   
                    Blob myqli fields type
                  -------------
                    BLOB: 252
                */     
             $data_fields_names_array = array_keys($data);
             $int_array = array(1,2,3,8,9,16); 
             $double_array = array(4,5,246); 
             $blob_array = array(249,250,251);
             $parameters_type = "";
             $values = array();
            foreach ($fields as $field)
            {   
                // Check if $field that is fetch by sql query inside array of data of isert and update
                if (in_array($field['orgname'],$data_fields_names_array))
                {
                    if (in_array($field['type'], $int_array))
                    {
                        $parameters_type .= "i";
                    }
                    elseif (in_array($field['type'], $double_array))
                    {
                        $parameters_type .= "d";
                    }
                    elseif (in_array($field['type'], $blob_array))
                    {
                        $parameters_type .= "b";
                    }
                    else
                    {
                        $parameters_type .= "s";
                    }

                    $values[] = $data[$field['orgname']];
                }
            } 
            $params = array($parameters_type);            
            return array_merge($params, $values);  // Merge $values array in $params array 

       }

               
        
        //! Check whether the input is an array whose keys are all integers.
        /*!
            \param[in] $InputArray          (array) Input array.
          \return                         (bool) \b true iff the input is an array whose keys are all integers.
         */
        private function IsArrayAllKeyInt($InputArray)
        {
            if(!is_array($InputArray))
            {
                 return false;
            }

            if(count($InputArray) <= 0)
            {
                 return true;
            }

            return array_unique(array_map("is_int", array_keys($InputArray))) === array(true);
        }
        
        //! Check whether the input is an array whose keys are all strings.
         /*!
        \param[in] $InputArray          (array) Input array.
        \return                         (bool) \b true iff the input is an array whose keys are all strings.
        */
        private function IsArrayAllKeyString($InputArray)
        {
            if(!is_array($InputArray))
            {
               return false;
            }

           if(count($InputArray) <= 0)
            {
               return true;
            }

            return array_unique(array_map("is_string", array_keys($InputArray))) === array(true);
        }

        //! Check whether the input is an array with at least one key being an integer and at least one key being a string.
        /*!
        \param[in] $InputArray          (array) Input array.
        \return                         (bool) \b true iff the input is an array with at least one key being an integer and at least one key being a string.
        */
        private function IsArraySomeKeyIntAndSomeKeyString($InputArray)
        {
            if(!is_array($InputArray))
            {
              return false;
            }

            if(count($InputArray) <= 0)
            {
              return true;
            }

            return count(array_unique(array_map("is_string", array_keys($InputArray)))) >= 2;
        }
        
        //! Check whether the input is an array whose keys are numeric, sequential, and zero-based.
        /*!
        \param[in] $InputArray          (array) Input array.
        \return                         (bool) \b true iff the input is an array whose keys are numeric, sequential, and zero-based.
        */
        private function IsArrayKeyNumericSequentialZeroBased($InputArray)
        {
            if(!is_array($InputArray))
            {
                return false;
            }

            if(count($InputArray) <= 0)
            {
                return true;
            }

            return array_keys($InputArray) === range(0, count($InputArray) - 1);
        }

        
        public function __destruct() {
            foreach ($this->query_obj As $obj)
            {
                unset($obj);
            }
                unset($this->connect);
        }


        
        
}
