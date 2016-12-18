<?php
/**
 * Created by PhpStorm.
 * User: Marian
 * Date: 27.11.2016
 * Time: 20:55
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class SecurityController extends \FOS\UserBundle\Controller\SecurityController {

	public function loginAction(Request $request) {
		return parent::loginAction($request);
	}

	public function logoutAction() {
		parent::logoutAction();
	}

}