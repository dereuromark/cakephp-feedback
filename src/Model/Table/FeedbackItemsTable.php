<?php
declare(strict_types = 1);

namespace Feedback\Model\Table;

use ArrayObject;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeedbackItems Model
 *
 * @method \Feedback\Model\Entity\FeedbackItem newEmptyEntity()
 * @method \Feedback\Model\Entity\FeedbackItem newEntity(array $data, array $options = [])
 * @method array<\Feedback\Model\Entity\FeedbackItem> newEntities(array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem get($primaryKey, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\Feedback\Model\Entity\FeedbackItem> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Feedback\Model\Entity\FeedbackItem saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\Feedback\Model\Entity\FeedbackItem>|false saveMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\Feedback\Model\Entity\FeedbackItem> saveManyOrFail(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\Feedback\Model\Entity\FeedbackItem>|false deleteMany(iterable $entities, $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\Feedback\Model\Entity\FeedbackItem> deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FeedbackItemsTable extends Table {

	/**
	 * @var array<mixed>
	 */
	protected array $order = [
		'created' => 'DESC',
	];

	/**
	 * @return \Cake\Database\Schema\TableSchemaInterface
	 */
	public function getSchema(): TableSchemaInterface {
		$schema = parent::getSchema();
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

		$this->_prefixOrderProperty();
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
			->scalar('priority')
			->allowEmptyString('priority');

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

	/**
	 * Sets the default ordering as 2.x shim.
	 *
	 * If you don't want that, don't call parent when overwriting it in extending classes.
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\Query\SelectQuery $query
	 * @param \ArrayObject $options
	 * @param bool $primary
	 * @return \Cake\ORM\Query\SelectQuery
	 */
	public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): SelectQuery {
		$order = $query->clause('order');
		if (($order === null || !count($order)) && !empty($this->order)) {
			$query->order($this->order);
		}

		return $query;
	}

	/**
	 * Prefixes the order property with the actual alias if its a string or array.
	 *
	 * The core fails on using the proper prefix when building the query with two
	 * different tables.
	 *
	 * @return void
	 */
	protected function _prefixOrderProperty(): void {
		foreach ($this->order as $key => $value) {
			if (is_numeric($key)) {
				$this->order[$key] = $this->_prefixAlias($value);
			} else {
				$newKey = $this->_prefixAlias($key);
				$this->order[$newKey] = $value;
				if ($newKey !== $key) {
					unset($this->order[$key]);
				}
			}
		}
	}

	/**
	 * Checks if a string of a field name contains a dot if not it will add it and add the alias prefix.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function _prefixAlias($string) {
		if (strpos($string, '.') === false) {
			return $this->getAlias() . '.' . $string;
		}

		return $string;
	}

}
