<?php

namespace SingleTableInheritance;

use TestCase;

class SingleTableInheritanceIntegrationTest extends TestCase
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
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
    }

    /** @test */
    public function instantiates_child_models()
    {
        User::create(['type' => Employee::class]);
        User::create(['type' => null]);

        $users = User::orderBy('type', 'DESC')->get();

        $this->assertCount(2, $users);
        $this->assertInstanceOf(Employee::class, $users->first());
        $this->assertInstanceOf(User::class, $users->last());
    }

    /** @test */
    public function child_model_keeps_scope()
    {
        User::create(['type' => Employee::class]);
        User::create(['type' => null]);

        $users = Employee::all();

        $this->assertCount(1, $users);
        $this->assertInstanceOf(Employee::class, $users->first());
    }

    /** @test */
    public function child_model_defaults_type_field()
    {
        $employee = Employee::create();

        $this->assertEquals(Employee::class, $employee->type);
    }

    /** @test */
    public function handles_multiple_children_models()
    {
        User::create();
        Employee::create();
        Admin::create();

        $users = User::orderBy('type', 'DESC')->get();

        $this->assertCount(3, $users);
        $this->assertInstanceOf(Employee::class, $users[0]);
        $this->assertInstanceOf(Admin::class, $users[1]);
        $this->assertInstanceOf(User::class, $users[2]);
    }

    /** @test */
    public function can_remove_scope()
    {
        User::create();
        Employee::create();

        $users = Employee::withoutGlobalScope(SingleTableInheritanceScope::class)
            ->orderBy('type', 'DESC')
            ->get();

        $this->assertCount(2, $users);
        $this->assertInstanceOf(Employee::class, $users[0]);
        $this->assertInstanceOf(User::class, $users[1]);
    }

    /** @test */
    public function passing_child_type_when_creating_returns_child_model()
    {
        $user = User::create(['type' => Employee::class]);
        $this->assertInstanceOf(Employee::class, $user);
    }
}