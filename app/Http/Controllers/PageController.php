<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{
    Shape,Pattern,Backstamp,
    Glaze,Color,Effect,User,
    Department,Requestor,Customer,
    ShapeType,Status,Process,
    GlazeOuter,GlazeInside,
    ItemGroup,Designer,ShapeCollection,
    Image
};


class PageController extends Controller
{
    public function dashboard() {
        // ดึงรายการล่าสุด 5 รายการของแต่ละโมเดล พร้อม updater
        $latestShapes = Shape::with(['updater'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $latestPatterns = Pattern::with(['updater'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $latestBackstamps = Backstamp::with(['updater'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $latestGlazes = Glaze::with(['updater'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // เพิ่ม count ของแต่ละ table
        $shapeCount = Shape::count();
        $activeShapeCount = Shape::where('status_id', '!=', 1)->count(); 
        $patternCount = Pattern::count();
        $activePatternCount = Pattern::where('status_id', '!=', 1)->count();
        $backstampCount = Backstamp::count();
        $activeBackstampCount = Backstamp::where('status_id', '!=', 1)->count();
        $glazeCount = Glaze::count();
        $activeGlazeCount = Glaze::where('status_id', '!=', 1)->count();
        // $userCount = User::count();

        // ---------- สร้างข้อมูลสำหรับกราฟ 30 วันล่าสุด ----------
        $today = Carbon::today();
        $start = $today->copy()->subDays(29); // รวมวันนี้ = 30 วันล่าสุด

        // นับจำนวนที่ถูกสร้างในแต่ละวันสำหรับแต่ละ model

        $shapeCountsByDate = Shape::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $patternCountsByDate = Pattern::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $backstampCountsByDate = Backstamp::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $glazeCountsByDate = Glaze::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();


        // เตรียม labels ($dates) และ values สำหรับแต่ละ dataset
        $dates = [];
        $shapeCounts = [];
        $patternCounts = [];
        $backstampCounts = [];
        $glazeCounts = [];
        // $userCounts = [];

        for ($i = 0; $i < 30; $i++) {
            $d = $start->copy()->addDays($i);
            $key = $d->format('Y-m-d');
            $dates[] = $d->format('d/m'); // label สำหรับแกน X
            
            $shapeCounts[] = $shapeCountsByDate[$key] ?? 0;
            $patternCounts[] = $patternCountsByDate[$key] ?? 0;
            $backstampCounts[] = $backstampCountsByDate[$key] ?? 0;
            $glazeCounts[] = $glazeCountsByDate[$key] ?? 0;
            // $userCounts[] = $userCountsByDate[$key] ?? 0;
        }

        return view('dashboard', compact(
            'latestShapes',
            'latestPatterns',
            'latestBackstamps',
            'latestGlazes',
            'shapeCount',
            'activeShapeCount',
            'patternCount',
            'activePatternCount',
            'backstampCount',
            'activeBackstampCount',
            'glazeCount',
            'activeGlazeCount',
            // 'userCount',
            'dates',
            'shapeCounts',
            'patternCounts',
            'backstampCounts',
            'glazeCounts',
            // 'userCounts'
        ));
    }


    // 🔹 User Management Controller
    public function user()
    {
        // ดึง user ทั้งหมดพร้อม roles + paginate
        $users = User::with(['roles','department', 'requestor', 'customer'])
            ->orderBy('id', 'asc')
            ->paginate(10);

        // สีแต่ละ permission
        $permissionColors = [
            'view' => 'bg-yellow-100 text-yellow-800',
            'edit' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'create' => 'bg-green-100 text-green-800',
            'file import' => 'bg-gray-100 text-gray-800',
            'file export' => 'bg-indigo-100 text-indigo-800',
            'manage users' => 'bg-purple-100 text-purple-800',
        ];

        $departments = Department::all();
        $requestors = Requestor::all();
        $customers = Customer::all();    
        // เพิ่ม property userPermissions ให้แต่ละ user (ค่อยแก้ให้ users มี roles_id))
        foreach ($users as $user) {
            $user->userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        }



        return view('user', compact('users', 'departments', 'requestors', 'customers', 'permissionColors'));
    }


    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'role'        => 'required|string|in:user,admin,superadmin',
            'permissions' => 'array',
            'department_id' => 'nullable|exists:departments,id',
            'requestor_id'  => 'nullable|exists:requestors,id',
            'customer_id'   => 'nullable|exists:customers,id',
        ]);

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
        $user->assignRole($data['role']);

        // Sync permissions
        $permissionsToAssign = $data['permissions'] ?? [];
        if (!in_array('view', $permissionsToAssign)) {
            $permissionsToAssign[] = 'view';
        }
        if (!in_array('file export', $permissionsToAssign)) {
            $permissionsToAssign[] = 'file export';
        }

        if ($data['role'] === 'superadmin') {
            $allPermissions = Permission::pluck('name')->toArray();
            $permissionsToAssign = array_unique(array_merge($permissionsToAssign, $allPermissions));
        }

        $user->syncPermissions($permissionsToAssign);

        return redirect()->back()->with('success', 'User created successfully!');
    }



    public function destroyUser(User $user)
    {
        $user->delete();

        return redirect()->route('user')->with('success', 'User deleted successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'password'      => 'nullable|string|min:6',
            'role'          => 'required|string|in:user,admin,superadmin',
            'permissions'   => 'nullable|array',
            'department_id' => 'nullable|exists:departments,id',
            'requestor_id'  => 'nullable|exists:requestors,id',
            'customer_id'   => 'nullable|exists:customers,id',
        ]);

        // อัปเดตข้อมูลพื้นฐาน
        $user->name          = $data['name'];
        $user->email         = $data['email'];
        $user->department_id = $data['department_id'] ?? null;
        $user->requestor_id  = $data['requestor_id'] ?? null;
        $user->customer_id   = $data['customer_id'] ?? null;

        // ถ้ามี password ใหม่ ให้ hash
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        // อัปเดต role และ permissions
        $user->syncRoles([$data['role']]);

        $permissionsToAssign = $data['permissions'] ?? [];
        if (!in_array('view', $permissionsToAssign)) {
            $permissionsToAssign[] = 'view';
        }
        if (!in_array('file export', $permissionsToAssign)) {
            $permissionsToAssign[] = 'file export';
        }

        if ($data['role'] === 'superadmin') {
            $allPermissions = Permission::pluck('name')->toArray();
            $permissionsToAssign = array_unique(array_merge($permissionsToAssign, $allPermissions));
        }

        $user->syncPermissions($permissionsToAssign);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function csvImport() {
        return view('csvImport');
    }

    public function getChartData(Request $request)
    {
        $days = $request->get('days', 30);
        $days = min(max((int)$days, 1), 365); // จำกัด 1-365 วัน

        // กำหนด interval ตามจำนวนวัน
        $interval = 1;
        if ($days == 60) {
            $interval = 2; // แสดงทุก 2 วัน
        } elseif ($days == 90) {
            $interval = 3; // แสดงทุก 3 วัน
        } elseif ($days == 180) {
            $interval = 6; // แสดงทุก 6 วัน
        } elseif ($days == 365) {
            $interval = 12; // แสดงทุก 12 วัน
        }

        $today = Carbon::today();
        $start = $today->copy()->subDays($days - 1);

        // นับจำนวนที่ถูกสร้างในแต่ละวัน
        $shapeCountsByDate = Shape::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $patternCountsByDate = Pattern::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $backstampCountsByDate = Backstamp::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $glazeCountsByDate = Glaze::whereBetween('created_at', [$start->startOfDay(), $today->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dates = [];
        $shapeCounts = [];
        $patternCounts = [];
        $backstampCounts = [];
        $glazeCounts = [];

        // รวมข้อมูลตาม interval
        for ($i = 0; $i < $days; $i += $interval) {
            $totalShape = 0;
            $totalPattern = 0;
            $totalBackstamp = 0;
            $totalGlaze = 0;
            
            // รวมข้อมูลของวันใน interval
            for ($j = 0; $j < $interval && ($i + $j) < $days; $j++) {
                $d = $start->copy()->addDays($i + $j);
                $key = $d->format('Y-m-d');
                
                $totalShape += $shapeCountsByDate[$key] ?? 0;
                $totalPattern += $patternCountsByDate[$key] ?? 0;
                $totalBackstamp += $backstampCountsByDate[$key] ?? 0;
                $totalGlaze += $glazeCountsByDate[$key] ?? 0;
            }
            
            $d = $start->copy()->addDays($i);
            $dates[] = $d->format('d/m');
            $shapeCounts[] = $totalShape;
            $patternCounts[] = $totalPattern;
            $backstampCounts[] = $totalBackstamp;
            $glazeCounts[] = $totalGlaze;
        }

        return response()->json([
            'dates' => $dates,
            'shapeCounts' => $shapeCounts,
            'patternCounts' => $patternCounts,
            'backstampCounts' => $backstampCounts,
            'glazeCounts' => $glazeCounts,
        ]);
    }
}
