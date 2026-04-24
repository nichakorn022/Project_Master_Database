<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\{User, Department, Requestor, Customer};

class UserController extends Controller
{
    // 🔹 User Management Controller
    public function user(Request $request)
    {
        $relations = [
            'roles','department', 'requestor', 'customer'
        ];

        // รับค่า perPage จาก request หรือใช้ default 10
        $perPage = $request->get('per_page', 10);
        // จำกัดค่า perPage ที่อนุญาต
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // รับค่า search
        $search = $request->get('search');
        $query = User::with($relations);
        // เพิ่ม search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('roles', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('permissions', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('department', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('requestor', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // ดึง user ทั้งหมดพร้อม roles + paginate
        $users = $query->latest()->paginate($perPage)->appends($request->query());

        $permissions = $this->getUserPermissions();
        $departments = Department::all();
        $requestors = Requestor::all();
        $customers = Customer::all();    
        // เพิ่ม property userPermissions ให้แต่ละ user (ค่อยแก้ให้ users มี roles_id))
        foreach ($users as $user) {
            $user->userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        }

        return view('user', compact('users', 'departments', 'requestors', 'customers', 'perPage', 'search'), $permissions);
    }

    private function handleNewSelectableData(array &$data)
    {
        $mapping = [
            'requestor_id' => [\App\Models\Requestor::class, 'name'],
            'department_id' => [\App\Models\Department::class, 'name'],
        ];
        foreach ($mapping as $field => [$model, $column]) {
            if (!empty($data[$field])) {
                $value = $data[$field];
                // ถ้าเป็นตัวเลข ให้เช็กว่า ID นั้นมีจริงไหม
                if (is_numeric($value)) {
                    $exists = $model::where('id', $value)->exists();
                    if (!$exists) {
                        // ถ้าไม่มีจริง ให้สร้างใหม่โดยใช้เลขนั้นเป็นชื่อ
                        $record = $model::create([$column => (string)$value]);
                        $data[$field] = $record->id;
                    }
                } else {
                    // ถ้าไม่ใช่ตัวเลข → เป็นชื่อใหม่แน่นอน → สร้างใหม่
                    $record = $model::create([$column => $value]);
                    $data[$field] = $record->id;
                }
            }
        }
        // Requestor
        if (!empty($data['requestor_id']) && !is_numeric($data['requestor_id'])) {
            $requestor = Requestor::create(['name' => $data['requestor_id']]);
            $data['requestor_id'] = $requestor->id;
        }
        // Department
        if (!empty($data['department_id']) && !is_numeric($data['department_id'])) {
            $department = Department::create(['name' => $data['department_id']]);
            $data['department_id'] = $department->id;
        }
    }

    private function rules($userId = null)
    {
        // ถ้าเป็น update (มี userId) ใช้ sometimes|nullable|string|min:6
        // sometimes จะ validate ก็ต่อเมื่อ field นี้มีอยู่ใน request และไม่เป็น null/empty
        $passwordRule = $userId 
            ? 'sometimes|nullable|string|min:6'  // update: validate เฉพาะเมื่อมีค่าและไม่ว่าง
            : 'required|string|min:6';            // create: required

        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $userId,
            'password'      => $passwordRule,
            'role'          => 'nullable|string|in:user,admin,superadmin',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
            'department_id' => 'nullable',
            'requestor_id'  => 'nullable',
            'customer_id'   => 'nullable|exists:customers,id',
        ];
    }

    private function messages()
    {
        return [
            'name.required' => __('controller.validation.name.required'),
            'name.max' => __('controller.validation.name.max'),
            'email.required' => __('controller.validation.email.required'),
            'email.email' => __('controller.validation.email.email'),
            'email.unique' => __('controller.validation.email.unique'),
            'password.required' => __('controller.validation.password.required'),
            'password.min' => __('controller.validation.password.min'),
            'role.required' => __('controller.validation.role.required'),
            'role.in' => __('controller.validation.role.in'),
            'permissions.array' => __('controller.validation.permissions.array'),
            'permissions.*.string' => __('controller.validation.permissions.*.string'),
            'department_id.exists' => __('controller.validation.department_id.exists'),
            'requestor_id.exists' => __('controller.validation.requestor_id.exists'),
            'customer_id.exists' => __('controller.validation.customer_id.exists'),
        ];
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        // ถ้าไม่ได้ส่ง role มา ให้ default เป็น 'user'
        $role = $data['role'] ?? 'user';
        // จัดการข้อมูล selectable fields ที่อาจเป็นข้อมูลใหม่
        $this->handleNewSelectableData($data);
        // สร้าง user ก่อน
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'department_id' => $data['department_id'] ?? null,
            'requestor_id'  => $data['requestor_id'] ?? null,
            'customer_id'   => $data['customer_id'] ?? null,
        ]);

        // Assign role
        $user->assignRole($role);

        // Sync permissions
        $permissionsToAssign = $data['permissions'] ?? [];
        if (!in_array('view', $permissionsToAssign)) {
            $permissionsToAssign[] = 'view';
        }
        if (!in_array('file export', $permissionsToAssign)) {
            $permissionsToAssign[] = 'file export';
        }

        if ($role === 'superadmin') {
            $allPermissions = Permission::pluck('name')->toArray();
            $permissionsToAssign = array_unique(array_merge($permissionsToAssign, $allPermissions));
        }

        $user->syncPermissions($permissionsToAssign);

        return response()->json([
            'status'  => 'success',
            'message' => __('controller.user.created'),
            'user'    => $user
        ], 201);
    }



    public function destroyUser(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('controller.user.deleted')
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate($this->rules($user->id), $this->messages());

        // อัพเดทข้อมูลพื้นฐาน
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'department_id' => $data['department_id'],
            'requestor_id' => $data['requestor_id'],
            'customer_id' => $data['customer_id'],
        ];

        // เพิ่ม password เฉพาะเมื่อมีการส่งมา
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        // จัดการข้อมูล selectable fields ที่อาจเป็นข้อมูลใหม่
        $this->handleNewSelectableData($updateData);
        $user->update($updateData);

        // อัปเดต role และ permissions
        $role = $data['role'] ?? 'user';
        $user->syncRoles([$role]);

        $permissionsToAssign = $data['permissions'] ?? [];
        if (!in_array('view', $permissionsToAssign)) {
            $permissionsToAssign[] = 'view';
        }
        if (!in_array('file export', $permissionsToAssign)) {
            $permissionsToAssign[] = 'file export';
        }

        if ($role === 'superadmin') {
            $allPermissions = Permission::pluck('name')->toArray();
            $permissionsToAssign = array_unique(array_merge($permissionsToAssign, $allPermissions));
        }

        $user->syncPermissions($permissionsToAssign);

        return response()->json([
            'status' => 'success',
            'message' => __('controller.user.updated'),
            'user' => $user->load(['roles', 'department', 'requestor', 'customer'])
        ]);
    }
}
