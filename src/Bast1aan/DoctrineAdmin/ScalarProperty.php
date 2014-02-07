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

	/**
	 * AssociationProperty represents a property of an @see Entity
	 * containing a scalar value
	 */
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
		 * Construct the ScalarProperty with the field name, the value and the
		 * Entity it belongs to
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

		/**
		 * {@inheritdoc}
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * Set the value of the property
		 *
		 * @param mixed $value
		 */
		public function setValue($value) {
			$this->value = $value;
		}

		/**
		 * The type name of the mapped field. Can be one of Doctrine's mapping types
		 * or a custom mapping type.
		 *
		 * @return string
		 */
		public function getType() {
			return $this->type;
		}

		/**
		 * Return the (maximum) length of this property, if applicable
		 *
		 * @return int
		 */
		public function getLength() {
			return $this->length;
		}

		/**
		 * {@inheritdoc}
		 */
		public function isNull() {
			return $this->value === null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function isNullable() {
			return $this->entity->getClassMetaData()->isNullable($this->getName());
		}

		/**
		 * Returns true if this property is (part of) the id (primary key) of the entity
		 *
		 * @return boolean
		 */
		public function isId() {
			return in_array($this->getName(), $this->entity->getIdentifierNames());
		}

		/**
		 * Returns true if this property is (part of) the id (primary key) of the entity
		 * and the id is auto-generated. In this case the property is not supposed
		 * to be set in any way
		 *
		 * @return boolean
		 */
		public function isAutoId() {
			return $this->isId() && $this->entity->getClassMetaData()->usesIdGenerator();
		}

		/**
		 * Get the entity where this property belongs to
		 * 
		 * @return Entity
		 */
		public function getEntity() {
			return $this->entity;
		}

	}
}