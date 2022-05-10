<?php
namespace Tests;
use Artisan;

class GeneralTest extends TestCase{
    
    public function setUp(){
        parent::setUp();
        $this->createDB();
        
       
    }

    protected function createDB(){
        
        Artisan::call('db:seed');

    }
}