<?php

namespace Bast1aan\DoctrineAdmin {
	abstract class AbstractProperty implements Property {
		
		private $name, $value, $type;
		
		/**
		 * @var DoctrineAdmin
		 */
		protected $doctrineAdmin;
		
		public function __construct($name, $value, $type, DoctrineAdmin $doctrineAdmin) {
			$this->name = $name;
			$this->value = $value;
			$this->type = $type;
			$this->doctrineAdmin = $doctrineAdmin;
		}
		
		public function __getString() {
			return (string) $this->getValue();
		}

		public function getName() {
			return $this->name;
		}

		public function getValue() {
			return $this->value;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function isNull() {
			return $this->value === null;
		}

	}
}