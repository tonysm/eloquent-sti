<?php

namespace SingleTableInheritance;

use TestCase;

class SingleTableInheritanceOtherAttributeIntegrationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createSchema();
    }

    private function createSchema()
    {
        $this->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('user_type')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
    }

    /** @test */
    public function can_change_the_inheritance_field()
    {
        UserWithOtherAttribute::create();
        EmployeeWithOtherAttribute::create();

        $users = UserWithOtherAttribute::all();
        $employees = EmployeeWithOtherAttribute::all();

        $this->assertCount(2, $users);
        $this->assertCount(1, $employees);
    }
}