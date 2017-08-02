<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mysqli_fields
{
        public $all = array();
        public $names = array();
        public $orgnames = array();
        public $primary = array();
     
        public function __construct($feildsArray)
    {
        $this->all = $feildsArray;
        $this->names();
        $this->orgname();
        $this->primary();
    }
    
         private function names()
    {
            $this->names = array(); 
            foreach ($this->all As $feild) 
            {
                $this->names[] =  $feild['name'];
            }
    }
    
    
             private function orgname()
    {
           $this->orgnames = array(); 
           foreach ($this->all As $feild) 
            {
                $this->orgnames[] =  $feild['orgname'];
            }
    }
    
    
    private function primary()
    {
        $this->primary = array(); 
        foreach ($this->all As $feild) 
        {
            if ($feild['flags'] & MYSQLI_PRI_KEY_FLAG) { 
                 $this->primary[] = $feild['name']; 
            }
        }
    }

   
    
    

}

