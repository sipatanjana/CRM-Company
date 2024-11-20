<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth:api',
            'role:super_admin'
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = Company::paginate();
        return CompanyResource::collection($model);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $data = $request->all();
        $company = Company::create($data);

        $company_name_lower = Str::lower(Str::replace(' ', '.', $company->name));

        $user = User::create([
            'email' => "$company_name_lower.manager.$company->email",
            'password' => Hash::make("$company_name_lower.manager.$company->email"),
        ]);
        $user->assignRole('manager');
        $user->assignRole('employe');

        $user->Employe()->create([
            'company_id' => $company->id,
            'name' => "$company->name Manager",
            'position' => "manager",
        ]);

        $model = Company::where('id', $company->id)->get();

        return CompanyResource::collection($model);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = Company::where('id', $id)->get();

        return CompanyResource::collection($model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $id)
    {
        $data = $request->all();
        $models = Company::where('id', $id)->first();
        if ($models) {
            $models->update($data);
            $model = Company::where('id', $models->id)->get();
            return CompanyResource::collection($model);
        } else return $this->failedFunction(['message' => 'Data Can\'t be Found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = Company::where('id', $id)->first();
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
