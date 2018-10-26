<?php

namespace CreativeServices\Workshop\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ApplicationInterface
{
    /**
     * @param Request $request
     * @return Response
     */
    public function respond(Request $request);
}