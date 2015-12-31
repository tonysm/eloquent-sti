<?php

namespace SingleTableInheritance;

use TestCase;
use Illuminate\Database\Eloquent\Model;

class RelationshipBelongsToIntegrationTest extends TestCase
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
        $this->schema()->create('groups', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
        $this->schema()->drop('groups');
    }

    /** @test */
    public function handles_belongs_to_relationships()
    {
        $user = BelongsToEmployee::create();
        $user->groups()->save(new BelongsToGroup);

        $group = BelongsToGroup::first();

        $this->assertInstanceOf(BelongsToEmployee::class, $group->user);
    }
}

class BelongsToUser extends Model {
    use SingleTableInheritance;

    protected $table = 'users';

    protected $fillable = ['type'];

    public function groups()
    {
        return $this->hasMany(BelongsToGroup::class, 'user_id');
    }
}

class BelongsToEmployee extends BelongsToUser {}

class BelongsToGroup extends Model {
    protected $table = 'groups';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}