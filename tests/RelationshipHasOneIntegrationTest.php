<?php

namespace SingleTableInheritance;

use TestCase;
use Illuminate\Database\Eloquent\Model;

class RelationshipHasOneIntegrationTest extends TestCase
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
            $table->unsignedInteger('phone_id')->nullable();
            $table->timestamps();
        });
        $this->schema()->create('phones', function ($table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
        $this->schema()->drop('phones');
    }

    /** @test */
    public function handles_has_one_on_base_model()
    {
        $phone = HasOnePhone::create();
        $phone->user()->save(HasOneUser::create());

        $this->assertInstanceOf(HasOneUser::class, $phone->user);
        $this->assertInstanceOf(HasOneUser::class, HasOnePhone::first()->user);
    }

    /** @test */
    public function handles_child_models()
    {
        $phone = HasOnePhone::create();
        $phone->user()->save(HasOneEmployee::create());

        $this->assertInstanceOf(HasOneEmployee::class, $phone->user);
        $this->assertInstanceOf(HasOneEmployee::class, HasOnePhone::first()->user);
    }
}

class HasOneUser extends Model
{
    use SingleTableInheritance;

    protected $table = 'users';

    protected $fillable = ['type', 'group_id'];
}

class HasOneEmployee extends HasOneUser {}

class HasOnePhone extends Model
{
    protected $table = 'phones';

    public function user()
    {
        return $this->hasOne(HasOneUser::class, 'phone_id');
    }
}