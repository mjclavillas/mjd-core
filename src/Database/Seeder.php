<?php
namespace Mark\MjdCore\Database;

use Faker\Factory;

abstract class Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    abstract public function run();
}