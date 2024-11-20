<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Http\Resources\EmployeResource;
use App\Models\Employe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth:api',
            new Middleware('role:super_admin|manager|employe', only: ['index', 'show']),
            new Middleware('role:manager', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->keywords ?: '';
        $user = User::find(Auth::user()->id);
        $model = Employe::where([['position', 'employe'], ['company_id', $user->Employe->company_id]])->search(strtolower($keyword))->paginate();

        return EmployeResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostEmployeRequest $request)
    {
        $user = User::find(Auth::user()->id);

        $data = $request->all();
        $data['position'] = 'employe';
        $data['company_id'] = $user->Employe->company_id;

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
        $model = Employe::where([['id', $id], ['position', 'employe']])->get();

        return EmployeResource::collection($model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeRequest $request, string $id)
    {
        $data = $request->only('name', 'address', 'phone_number');
        $models = Employe::where([['id', $id], ['position', 'employe']])->first();

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
        $model = Employe::where([['id', $id], ['position', 'employe']])->first();
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
