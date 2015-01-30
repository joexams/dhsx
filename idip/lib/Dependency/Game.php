<?php
class Dependency_Game extends DependencyAbstract
{
	protected function setup()
	{
		parent::setup();

		if(!$this->registry->offsetExists('gamesql'))
		{
			$sql = new Sql(new Sql_Driver_Mysqli(), $this->config['sql_host1'], $this->config['sql_user1'], $this->config['sql_pw1'], $this->config['sql_db1']);

			$this->registry->offsetSet('gamesql', $sql);
		}
	}
}
