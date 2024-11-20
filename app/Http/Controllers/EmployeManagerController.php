<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostEmployeManagerRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Http\Resources\EmployeResource;
use App\Models\Employe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeManagerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('role:super_admin|manager', only: ['index', 'show']),
            new Middleware('role:super_admin', only: ['store', 'update', 'destroy']),
            new Middleware('role:manager', only: ['edit']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->keywords ?: '';
        $model = Employe::where('position', 'manager')->search(strtolower($keyword))->paginate();

        return EmployeResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostEmployeManagerRequest $request)
    {
        $data = $request->all();
        $data['position'] = 'manager';

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $employe = $user->Employe()->create($data);

        $model = Employe::where('id', $employe->id)->get();
        return EmployeResource::collection($model);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = Employe::where([['id', $id], ['position', 'manager']])->get();

        return EmployeResource::collection($model);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpdateEmployeRequest $request)
    {
        $data = $request->only('name', 'address', 'phone_number');
        $user = User::find(Auth::user()->id);

        $models = Employe::where('id', $user->Employe->id)->first();

        if ($models) {
            $models->update($data);
            $model = Employe::where('id', $models->id)->get();
            return EmployeResource::collection($model);
        } else return $this->failedFunction(['message' => 'Data Can\'t be Found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeRequest $request, string $id)
    {
        $data = $request->only('name', 'address', 'phone_number');
        $models = Employe::where('id', $id)->first();

        if ($models) {
            $models->update($data);
            $model = Employe::where('id', $models->id)->get();
            return EmployeResource::collection($model);
        } else return $this->failedFunction(['message' => 'Data Can\'t be Found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Employe::where('id', $id)->first();
        if ($model) {
            try {
                $model->delete();
                return $this->succesFunction(['message' => 'Data successfully erased']);
            } catch (\Throwable $th) {
                return $this->failedFunction(['message' => 'Cannot be deleted, Data is still in use'], 401);
            }
        } else return $this->failedFunction(['message' => 'Data Can\'t be Found'], 404);
    }
}
