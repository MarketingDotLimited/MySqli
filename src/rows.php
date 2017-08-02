<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mysqli_rows
{
    public $data = array();
    public $ndata = array();
    
    public function __construct($rowsArray)
    {
        $this->data = $rowsArray;
        $this->ndata();
    }
    
    private function ndata()
    {
        $this->ndata = array();
        foreach ($this->data  as $row)
        {
          $fields_Data = array();
          foreach ($row as  $field) {
              $fields_Data[] = $field;
          }

            $this->ndata[] = $fields_Data;
        }
    }
    
    
    
    
}