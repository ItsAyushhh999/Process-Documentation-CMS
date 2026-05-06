<?php

namespace App\Http\Traits;

use App\Models\User;

trait DepartmentCheck
{
    public function CheckDepartment($id)
    {
        $permission = User::selectRaw('
        (select department_name from departments where departments.id = users.department_id limit 1) as departmentName')->find($id);
        if ($permission->departmentName == 'Project Administration') {
            return true;
        }

        return false;
    }

    public function CheckPermission($id)
    {
        $permission = User::selectRaw('
        (select department_name from departments where departments.id = users.department_id limit 1) as departmentName')->find($id);
        if ($permission->departmentName == 'Project Administration' || $permission->departmentName == 'Project Documentation') {
            return true;
        }

        return false;
    }
}
