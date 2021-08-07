<?php

namespace App\Services;

use DarlanThiago\MicroservicesCommon\Services\Traits\ConsumeExternalService;

class EvaluationService
{
    use ConsumeExternalService;

    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = config('services.micro_02.url');
        $this->token = config('services.micro_02.token');
    }

    public function getEvaluationsCompany(string $company)
    {

        $endPoint = $this->url . "/evaluations/{$company}";

        $method = 'get';

        $response = $this->request($method, $endPoint);

        return $response;
    }
}
