<?php

namespace Bast1aan\DoctrineAdmin {
	use Countable;
	use Iterator;
	use Doctrine\Common\Collections\Collection as DoctrineCollection;
	
	class CollectionAssociationProperty extends AbstractProperty implements Iterator, Countable {
		
		/**
		 * @var array
		 */
		private $value;
		
		/**
		 *
		 * @var int
		 */
		private $i;
		
		public function __construct($name, $value, $type, DoctrineAdmin $doctrineAdmin) {
			parent::__construct($name, $value, $type, $doctrineAdmin);
			if (is_array($value)) {
				$this->value = $value;
			} elseif ($value instanceof DoctrineCollection) {
				$this->value = $value->toArray();
			} else {
				return new Exception('Collection property value not an instance of Doctrine\Common\Collections\Collection and not an array');
			}
		}
		
		public function count() {
			count($this->value);
		}
		
		public function current() {
			return new Entity($this->value[$this->i], $this->doctrineAdmin);
		}

		public function key() {
			return $this->i;
		}

		public function next() {
			++$this->i;
		}

		public function rewind() {
			$this->i = 0;
		}

		public function valid() {
			return isset($this->value[$this->i]);
		}

	
	}
}