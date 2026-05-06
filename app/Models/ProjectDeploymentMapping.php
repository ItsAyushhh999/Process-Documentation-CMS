<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDeploymentMapping extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * returns arn with specific to account.
     *
     * @return void
     */
    public function getAccountArnAttribute()
    {
        $account = $this->attributes['account_identifier'] == '0' ? 'voxmg' : 'voxships';

        return config("app.role_arns.$account");
    }
}
