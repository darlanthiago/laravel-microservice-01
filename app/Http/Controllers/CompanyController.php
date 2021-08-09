<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use App\Models\Company;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        protected Company $repository,
        protected EvaluationService $evaluationService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = $this->repository->getCompanies($request->get('filter', ''));

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCompany $request)
    {
        $company = $this->repository->create($request->validated());

        CompanyCreated::dispatch($company->email)->onQueue('queue_email');

        return new CompanyResource($company);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $company = $this->repository->where('uuid', $uuid)->with('category')->firstOrFail();

        $evaluations = $this->evaluationService->getEvaluationsCompany($uuid)->json();

        return (new CompanyResource($company))
            ->additional([
                'evaluations' => $evaluations['data']
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCompany $request, $uuid)
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        $company->update($request->validated());

        return new CompanyResource($company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        $company->delete();

        return response(null, 204);
    }
}
