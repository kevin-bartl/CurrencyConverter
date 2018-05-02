<?php

namespace App\Controller;

use App\Service\CurrencyConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends Controller
{
    /**
     * @Route("/currency/convert")
     * @param Request $request
     * @param CurrencyConverter $currencyConverter
     * @return Response
     */
    public function convertAction(Request $request, CurrencyConverter $currencyConverter): Response
    {
        $viewParams = [];
        if ($request->isMethod(Request::METHOD_POST)) {
            $amount = $request->get('amount');
            if (false === filter_var($amount, FILTER_VALIDATE_FLOAT)) {
                $viewParams['errorMessage'] = "Not a valid amount: <$amount>";
                return $this->render('currency/convert.html.twig', $viewParams);
            }

            $baseCur = strtoupper($request->get('base'));
            if (0 === preg_match('/([A-Z]){3}/', $baseCur)) {
                $viewParams['errorMessage'] = "Not a valid currency: <$baseCur>";
                return $this->render('currency/convert.html.twig', $viewParams);
            }
            $targetCur = strtoupper($request->get('target'));
            if (0 === preg_match('/([A-Z]){3}/', $targetCur, $matches)) {
                $viewParams['errorMessage'] = "Not a valid currency: <$targetCur>";
                return $this->render('currency/convert.html.twig', $viewParams);
            }
            try {
                $viewParams['converted'] = $currencyConverter->convert($amount, $baseCur, $targetCur);
            } catch (\Exception $exception) {
                $viewParams['errorMessage'] = $exception->getMessage();
            }
        }
        return $this->render('currency/convert.html.twig', $viewParams);
    }
}
