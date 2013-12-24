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

namespace Bast1aan\DoctrineAdmin\View {
	use Bast1aan\DoctrineAdmin\Entity;
	use Bast1aan\DoctrineAdmin\Exception;
	use Bast1aan\DoctrineAdmin\Property;
	use Bast1aan\DoctrineAdmin\ScalarProperty;
	use Bast1aan\DoctrineAdmin\AssociationProperty;
	use Bast1aan\DoctrineAdmin\CollectionAssociationProperty;
	use Bast1aan\DoctrineAdmin\DoctrineAdmin;
	use DateTime;
	use Doctrine\DBAL\Types\Type;
	
	class View {
		
		const FORMAT_DATE = 'Y-m-d';
		const FORMAT_DATETIME = 'Y-m-d H:i:s';
		const FORMAT_TIME = 'H:i:s';

		/**
		 * @var DoctrineAdmin;
		 */
		private $doctrineAdmin;

		/**
		 * @param DoctrineAdmin $doctrineAdmin
		 */
		public function setDoctrineAdmin($doctrineAdmin)
		{
			$this->doctrineAdmin = $doctrineAdmin;
		}

		/**
		 * @return DoctrineAdmin
		 */
		public function getDoctrineAdmin()
		{
			return $this->doctrineAdmin;
		}

		public function __construct(DoctrineAdmin $doctrineAdmin = null) {
			$this->doctrineAdmin = $doctrineAdmin;
		}

		/**
		 * @return Form
		 */
		public function getForm() {
			return new Form($this);
		}
		
		/**
		 * 
		 * @param Property $property
		 * @param Form $form
		 * @return FormElement
		 */
		public function getFormElement(Property $property, Form $form) {
			if ($property instanceof ScalarProperty) {
				return $this->getScalarFormElement($property, $form);
			} elseif ($property instanceof AssociationProperty) {
				return $this->getAssociationFormElement($property, $form);
			}
		}
		
		/**
		 * 
		 * @param ScalarProperty $property
		 * @param Form $form
		 * @return FormElementScalar
		 */
		protected function getScalarFormElement(ScalarProperty $property, Form $form) {
			switch($property->getType()) {
				case Type::STRING:
					$length = $property->getLength();
					// for strings without a known length or with a length larger than 255
					// create a textarea
					if ($length == 0 || $length > 255) {
						return new FormElementTextArea($property, $form);
					}
				default:
					return new FormElementScalar($property, $form);
			}
		}

		/**
		 * 
		 * @param AssociationProperty $property
		 * @param Form $form
		 * @return FormElementAssociation
		 */
		protected function getAssociationFormElement(AssociationProperty $property, Form $form) {
			if ($property instanceof CollectionAssociationProperty) {
				return new FormElementCollectionAssociation($property, $form);
			} elseif($property instanceof AssociationProperty) {
				return new FormElementAssociation($property, $form);
			}
		}
		
		/**
		 * @param ScalarProperty $property
		 * @return string
		 */
		public function formatScalarPropertyToString(ScalarProperty $property) {
			if ($property->isNull()) {
				return null;
			}
			switch($property->getType()) {
				case Type::DATE:
					$dateTime = $this->getDateTimeFromProperty($property);
					return $dateTime->format(self::FORMAT_DATE);
				case Type::DATETIME:
				case Type::DATETIMETZ:
					$dateTime = $this->getDateTimeFromProperty($property);
					return $dateTime->format(self::FORMAT_DATETIME);
				case Type::TIME:
					$dateTime = $this->getDateTimeFromProperty($property);
					return $dateTime->format(self::FORMAT_TIME);
				default:
					return (string) $property->getValue();
			}
		}
		
		/**
		 * @param string $string
		 * @param ScalarProperty $property
		 */
		public function formatStringToScalarProperty($string, ScalarProperty& $property) {
			switch($property->getType()) {
				case Type::DATE:
					$property->setValue(DateTime::createFromFormat(self::FORMAT_DATE, $string));
					return;
				case Type::DATETIME:
				case Type::DATETIMETZ:
					$property->setValue(DateTime::createFromFormat(self::FORMAT_DATETIME, $string));
					return;
				case Type::TIME:
					$property->setValue(DateTime::createFromFormat(self::FORMAT_TIME, $string));
					return;
				default:
					$property->setValue($string);
			}
		}
		
		/**
		 * @param ScalarProperty $property
		 * @return DateTime
		 * @throws Exception
		 */
		private function getDateTimeFromProperty(ScalarProperty $property) {
			$dateTime = $property->getValue();
			if (!$dateTime instanceof DateTime) {
				throw new Exception(sprintf('Value of property with name %s is not a DateTime', $property->getName()));
			}
			return $dateTime;
		}
		
		/**
		 * @param Entity $entity
		 * @return string
		 */
		public function renderEntity(Entity $entity) {
			return get_class($entity->getOriginalEntity()) . '_' . $this->renderEntityId($entity);
		}
		
		/**
		 * @param Entity $entity
		 * @return string
		 */
		public function renderEntityId(Entity $entity) {
			$idValues = array();
			// escape the - so it won't be recognized as a compound separator
			foreach($entity->getIdentifierValues() as $idValue)
				$idValues[] = str_replace('-', '--', $idValue);
			
			return implode('-', $idValues);
			
		}

		/**
		 * @param string $entityName
		 * @param string $entityId
		 * @return Entity
		 * @throws Exception
		 */
		public function getEntityById($entityName, $entityId) {
			$em = $this->doctrineAdmin->getEntityManager();
			$classMetaData = $em->getClassMetadata($entityName);
			
			$idNames = $classMetaData->getIdentifierFieldNames();
			
			$idValues = explode('-', $entityId);
			
			if (count($idNames) != count($idValues)) {
				throw new Exception('Primary key values don\'t match amount of primary key fields');
			}
			
			$id = array();
			for($i = 0; $i < count($idNames); ++$i)
				$id[$idNames[$i]] = str_replace('--', '-', $idValues[$i]);
			
			return $this->doctrineAdmin->find($entityName, $id);
		}
	}
}