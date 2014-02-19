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
	 * containing an association with another entity.
	 */
	class AssociationProperty implements Property {

		/**
		 * @var bool
		 */
		private $readonly = false;

		/**
		 * @var Entity 
		 */
		private $entity;
		
		/**
		 * name of the association
		 * @var string
		 */
		private $name;
		
		/**
		 *
		 * @var object target entity
		 */
		private $value;
		
		/**
		 * Classname of the entity of the association
		 * @var string 
		 */
		private $entityName;
		
		/**
		 * @var DoctrineAdmin
		 */
		protected $doctrineAdmin;
		
		/**
		 * Construct an AssociationProperty with a field name, a value containing the
		 * associated entity object, and the Entity this property belongs to
		 * Be aware the value should be a bare entity object, NOT an instance of @see Entity
		 *
		 * @param string $name
		 * @param object $value
		 * @param Entity $entity
		 */
		public function __construct($name, $value, Entity $entity) {
			$this->name = $name;
			$this->value = $value;
			$this->entity = $entity;
			$this->entityName = $entity->getClassMetaData()->getAssociationTargetClass($name);
			$this->doctrineAdmin = $entity->getDoctrineAdmin();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * Return the value of the property, in this case an Entity containing
		 * the association
		 * @return Entity
		 */
		public function getValue() {
			if ($this->value != null)
				return Entity::factory($this->value, $this->doctrineAdmin);
		}
		
		/**
		 * Set the value of the property with an entity.
		 * Can be either object or instance of Entity
		 *
		 * @param Entity|object $value the entity
		 */
		public function setValue($value) {
			if ($value instanceof Entity)
				$value = $value->getOriginalEntity();

			if ($this->isInverseRelation()) {
				if ($value == null && $this->value != null) {
					// value will be set to null, while before it wasn't.
					// make sure the owning side doesn't contain the entity of this property
					$associationMapping = $this->getAssociationMapping();
					$mappedByName = $associationMapping['mappedBy'];
					$reverseEntity = $this->getValue();
					$reverseProp = $reverseEntity->getColumn($mappedByName);
					if ($reverseProp instanceof CollectionAssociationProperty) {
						do {
							$item = $reverseProp->current();
							if ($item->getOriginalEntity() == $this->value) {
								$item->delete();
							}
						} while ($reverseProp->next() || $reverseProp->valid());
					} elseif ($reverseProp instanceof AssociationProperty) {
						$reverseProp->setValue(null);
					}
					$reverseEntity->setColumn($reverseProp);
				}
			}

			if ($this->value != $value) {
				$this->value = $value;

				if ($this->isInverseRelation() && $this->value != null) {
					// value will be set to null, while before it wasn't.
					// make sure the owning side doesn't contain the entity of this property
					$associationMapping = $this->getAssociationMapping();
					$mappedByName = $associationMapping['mappedBy'];
					$reverseEntity = $this->getValue();
					$reverseProp = $reverseEntity->getColumn($mappedByName);
					if ($reverseProp instanceof CollectionAssociationProperty) {
						do {
							$item = $reverseProp->current();
							if ($item->getOriginalEntity() == $this->value) {
								$item->delete();
							}
						} while ($reverseProp->next() || $reverseProp->valid());
						$reverseProp->add($this->value);
					} elseif ($reverseProp instanceof AssociationProperty) {
						$reverseProp->setValue(null);
					}
					$reverseEntity->setColumn($reverseProp);
				}
			}
		}


		/**
		 * Get the (class) name of the entity of this association.
		 *
		 * @return string
		 */
		public function getEntityName() {
			return $this->entityName;
		}

		/**
		 * {@inheritdoc}
		 */
		public function isNull() {
			return $this->value === null;
		}

		/**
		 * Determine if this association is allowed to be set to null.
		 *
		 * @return boolean
		 */
		public function isNullable() {
			$associationMapping = $this->getAssociationMapping();
			// single-associatons are default nullable, except when explicitly false.
			// See http://docs.doctrine-project.org/en/latest/reference/annotations-reference.html#annref-joincolumn
			if (isset($associationMapping['joinColumns']) &&
				isset($associationMapping['joinColumns']['nullable']) &&
				$associationMapping['joinColumns']['nullable'] === false)
				return false;
			else
				return true;
		}

		/**
		 * Return the entity this property is belonging to
		 *
		 * @return Entity
		 */
		public function getEntity() {
			return $this->entity;
		}

		/**
		 * Determines if this association is read-only, or mark this item as read-only.
		 * For now, a association is always readonly if it is the inverse side of the association.
		 * Doctrine won't save the changes in this case.
		 *
		 * @param boolean $readonly
		 * @return boolean
		 */
		public function isReadOnly($readonly = null) {
			if ($readonly !== null) {
				$this->readonly = $readonly;
			}
			return $this->readonly;
		}

		/**
		 * Get the owning side of this property
		 *
		 * @return AssociationProperty
		 */
		private function getOwningSideProperty() {
			$associationMapping = $this->getAssociationMapping();
			if (isset($associationMapping['mappedBy'])) {

			}
		}

		/**
		 * Is this AssociationProperty coming from the inverse side?
		 *
		 * @return boolean
		 */
		private function isInverseRelation() {
			return $this->getClassMetaData()->isAssociationInverseSide($this->name);
		}

		/**
		 * Get the ClassMetadata from the entity
		 *
		 * @return \Doctrine\ORM\Mapping\ClassMetadata
		 */
		private function getClassMetaData() {
			return $this->entity->getClassMetaData();
		}

		/**
		 * Get the association mapping
		 *
		 * @return array
		 */
		private function getAssociationMapping() {
			return $this->getClassMetaData()->getAssociationMapping($this->name);
		}

	}
}