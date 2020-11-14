<?php
declare(strict_types = 1);

namespace Feedback\Model\Table;

use Cake\Database\Schema\TableSchemaInterface;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeedbackItems Model
 *
 * @method \Feedback\Model\Entity\FeedbackItem newEmptyEntity()
 * @method \Feedback\Model\Entity\FeedbackItem newEntity(array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[] newEntities(array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem get($primaryKey, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FeedbackItemsTable extends Table {

	/**
	 * @param \Cake\Database\Schema\TableSchema $schema
	 *
	 * @return \Cake\Database\Schema\TableSchema
	 */
	protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface {
		$schema->setColumnType('data', 'json');

		return $schema;
	}

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void {
		parent::initialize($config);

		$this->setTable('feedback_items');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Timestamp');
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): Validator {
		$validator
			->integer('id')
			->allowEmptyString('id', null, 'create');

		$validator
			->scalar('sid')
			->maxLength('sid', 128)
			->requirePresence('sid', 'create')
			->notEmptyString('sid');

		$validator
			->scalar('url')
			->maxLength('url', 190)
			->requirePresence('url', 'create')
			->notEmptyString('url');

		$validator
			->scalar('name')
			->maxLength('name', 120)
			->allowEmptyString('name');

		$validator
			->email('email')
			->allowEmptyString('email');

		$validator
			->scalar('subject')
			->maxLength('subject', 150)
			->allowEmptyString('subject');

		$validator
			->scalar('feedback')
			->allowEmptyString('feedback');

		$validator
			->integer('status')
			->allowEmptyString('status');

		return $validator;
	}

}
