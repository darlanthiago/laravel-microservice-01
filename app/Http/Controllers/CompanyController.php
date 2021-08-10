<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use Illuminate\Http\Request;

use App\Services\{
    CompanyService,
    EvaluationService
};

class CompanyController extends Controller
{
    public function __construct(
        protected EvaluationService $evaluationService,
        protected CompanyService $companyService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = $this->companyService->getCompanies($request->get('filter', ''));

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
        $company = $this->companyService
            ->createNewCompany($request->validated(), $request->image);

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
        $company = $this->companyService->getCompanyByUUID($uuid);

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
    public function update(StoreUpdateCompany $request, string $uuid)
    {

        $company = $this->companyService
            ->updateCompany($uuid, $request->validated(), $request->image);

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
        $this->companyService->deleteCompany($uuid);

        return response(null, 204);
    }
}
