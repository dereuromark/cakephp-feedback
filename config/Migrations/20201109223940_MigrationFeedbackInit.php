<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * This is only an example/default table. You can use your own on app level.
 */
class MigrationFeedbackInit extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
    	$this->table('feedback_items')
			->addColumn('sid', 'string', [
				'default' => null,
				'limit' => 128,
				'null' => false,
			])
			->addColumn('url', 'string', [
				'default' => null,
				'limit' => 190,
				'null' => false,
			])
			->addColumn('name', 'string', [
				'default' => null,
				'limit' => 120,
				'null' => true,
			])
			->addColumn('email', 'string', [
				'default' => null,
				'limit' => 120,
				'null' => true,
			])
			->addColumn('subject', 'string', [
				'default' => null,
				'limit' => 150,
				'null' => true,
			])
			->addColumn('feedback', 'text', [
				'default' => null,
				'limit' => MysqlAdapter::TEXT_REGULAR,
				'null' => true,
			])
			->addColumn('data', 'text', [
				'default' => null,
				'limit' => MysqlAdapter::TEXT_MEDIUM, // Only needed for MySQL to store more than 65k
				'null' => true,
			])
		   ->addColumn('priority', 'string', [
				'default' => null,
				'limit' => 20,
				'null' => true,
			])
			->addColumn('status', 'integer', [
				'default' => 0,
				'limit' => 2,
				'null' => false,
			])
			->addColumn('created', 'datetime', [
				'default' => null,
				'null' => false,
			])
			->create();
    }
}
