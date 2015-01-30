<?php 
abstract class ModuleAbstract
{
	protected $base;
	protected $basePath;
	protected $uriFragments = array();

	public function __construct(Base $base, $basePath, array $uriFragments)
	{
		$this->base         = $base;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;

		// assign dependencies
		$dependencies = $this->getDependencies();

		if($dependencies instanceof DependencyAbstract)
		{
			$args = $dependencies->getArgs();

			foreach($args as $k => $obj)
			{
				$this->$k = $obj;
			}
		}
	}

	public function getDependencies()
	{
		return null;
	}

	protected function getBasePath()
	{
		return $this->basePath;
	}

	protected function getUriFragments()
	{
		return $this->uriFragments;
	}

	public function _ini()
	{
		$this->onLoad();

		switch(Base::getRequestMethod())
		{
			case 'GET':

				$this->onGet();

				break;

			case 'POST':

				$this->onPost();

				break;

			case 'PUT':

				$this->onPut();

				break;

			case 'DELETE':

				$this->onDelete();

				break;
		}
	}

	public function onLoad()
	{
	}

	public function onGet()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	public function onDelete()
	{
	}

	public function processResponse($content)
	{
		return $content;
	}
}