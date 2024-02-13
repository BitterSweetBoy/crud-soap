<?php
    Class Conectar{
        protected $db;

        protected function conexion(){
            try{
                $conectar = $this->db = new PDO('mysql:host=localhost;dbname=redes','root','');
                return $conectar;
            }catch(Exception $e){
                print "Error: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        public function set_names(){
            return $this->db->query("SET NAMES 'utf8'");
        }
    }
?>