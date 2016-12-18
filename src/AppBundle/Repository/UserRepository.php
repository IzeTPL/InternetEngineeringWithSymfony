<?php
/**
 * Created by PhpStorm.
 * User: Marian
 * Date: 11.12.2016
 * Time: 03:45
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use Doctrine\ORM\EntityRepository as ORM;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class UserRepository extends ORM{

	public function query() {

		return $this->getEntityManager()
			->createQuery('
			SELECT i
            FROM AppBundle:User i
            ORDER BY i.id DESC
		');

	}

	public function paginate($page = 1)
	{

		$paginator = new Pagerfanta(new DoctrineORMAdapter($this->query(), false));
		$paginator->setMaxPerPage(10);
		$paginator->setCurrentPage($page);

		return $paginator;
	}

}