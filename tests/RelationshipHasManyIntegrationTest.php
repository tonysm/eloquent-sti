<?php

namespace SingleTableInheritance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use TestCase;

class RelationshipHasManyIntegrationTest extends TestCase
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
            $table->unsignedInteger('group_id')->nullable();
            $table->timestamps();
        });
        $this->schema()->create('groups', function ($table) {
            $table->increments('id');

            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
        $this->schema()->drop('groups');
    }

    /** @test */
    public function handles_has_many()
    {
        $group = HasManyGroup::create();
        HasManyUser::create(['group_id' => $group->id]);
        HasManyEmployee::create(['group_id' => $group->id]);

        $users = $group->users;

        $this->assertCount(2, $users);
        $this->assertInstanceOf(HasManyEmployee::class, $users->first());
        $this->assertInstanceOf(HasManyUser::class, $users->last());
    }

    /** @test */
    public function creating_via_relationship_results_in_child_model()
    {
        $group = HasManyGroup::create();
        $employee = $group->users()->create(['type' => HasManyEmployee::class]);
        $user = $group->users()->create([]);

        $this->assertInstanceOf(HasManyEmployee::class, $employee);
        $this->assertInstanceOf(HasManyUser::class, $user);
        $this->assertNotInstanceOf(HasManyEmployee::class, $user);
    }
}

class HasManyUser extends Model
{
    use SingleTableInheritance;

    protected $table = 'users';

    protected $fillable = ['type', 'group_id'];
}

class HasManyEmployee extends HasManyUser {}

class HasManyGroup extends Model
{
    protected $table = 'groups';

    public function users()
    {
        return $this->hasMany(HasManyUser::class, 'group_id')->orderBy('type', 'DESC');
    }
}