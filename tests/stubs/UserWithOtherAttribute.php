<?php

namespace SingleTableInheritance;

use Illuminate\Database\Eloquent\Model;

class UserWithOtherAttribute extends Model
{
    use SingleTableInheritance;

    protected $table = 'users';

    protected $fillable = ['type'];

    protected static function getInheritanceField()
    {
        return 'user_type';
    }
}