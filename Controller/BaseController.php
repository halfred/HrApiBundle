<?php

namespace Hr\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends Controller
{
    /**
     * @var string
     */
    protected $defaultEntitySerializationGroup;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->setDefaultEntitySerializationGroup();
    }

    /**
     *
     */
    protected function setDefaultEntitySerializationGroup(): void
    {
        $defaultEntitySerializationGroup = explode('\\', get_class($this));
        $defaultEntitySerializationGroup = lcfirst(array_pop($defaultEntitySerializationGroup));
        $this->defaultEntitySerializationGroup = str_replace('Controller', '', $defaultEntitySerializationGroup);
    }
}
