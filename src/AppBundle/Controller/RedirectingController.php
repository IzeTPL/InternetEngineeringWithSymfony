<?php
/**
 * Created by PhpStorm.
 * User: Marian
 * Date: 27.11.2016
 * Time: 23:49
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectingController extends Controller
{

	public function redirectToDefaultLanguageAction(Request $request) {

			$pathInfo = $request->getPathInfo();
			$requestUri = $request->getRequestUri();
			$locale = $request->getLocale();

			$url = str_replace($pathInfo, '/' . $locale . $requestUri, $requestUri);
			return $this->redirect($url, 301);

	}

	public function redirectToMainPageAction() {

		return $this->redirectToRoute('multi_blog_index');

	}

}