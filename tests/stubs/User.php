<?php

namespace SingleTableInheritance;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use SingleTableInheritance;

    protected $table = 'users';

    protected $fillable = ['type'];
}