<?php
/*
 * Doctrine Admin
 * Copyright (C) 2013 Bastiaan Welmers, bastiaan@welmers.net
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Bast1aan\DoctrineAdmin {
	use DateTime;
	class ScalarProperty implements Property {
		
		/**
		 * @var Entity
		 */
		private $entity;
		
		/**
		 * Name of the property.
		 * @var string
		 */
		private $name;
		
		/**
		 * Value of the property
		 * @var mixed 
		 */
		private $value;
		
		/**
		 * One of the doctrine types found in class constants of @see \Doctrine\DBAL\Types\Type
		 * @var string
		 */
		private $type;
		
		/**
		 * Length of the database field
		 * @var integer
		 */
		private $length;
		
		/**
		 * @var DoctrineAdmin
		 */
		protected $doctrineAdmin;
		
		/**
		 * 
		 * @param string $name
		 * @param mixed $value
		 * @param Entity $entity
		 */
		public function __construct($name, $value, Entity $entity) {
			$this->name = $name;
			$this->value = $value;
			$this->entity = $entity;
			$fieldMapping = $entity->getClassMetaData()->getFieldMapping($name);
			$this->type = $fieldMapping['type'];
			$this->length = $fieldMapping['length'];
			$this->doctrineAdmin = $entity->getDoctrineAdmin();
		}
		
		public function getName() {
			return $this->name;
		}

		public function getValue() {
			return $this->value;
		}
		
		public function setValue($value) {
			$this->value = $value;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getLength() {
			return $this->length;
		}
		
		public function isNull() {
			return $this->value === null;
		}
		
		public function isNullable() {
			return $this->entity->getClassMetaData()->isNullable($this->getName());
		}

		public function isId() {
			return in_array($this->getName(), $this->entity->getIdentifierNames());
		}

		public function isAutoId() {
			return $this->isId() && $this->entity->getClassMetaData()->usesIdGenerator();
		}
		
	}
}