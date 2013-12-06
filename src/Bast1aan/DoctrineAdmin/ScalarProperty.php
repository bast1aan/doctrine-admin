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
		 * @param string $type
		 * @param int $length
		 * @param DoctrineAdmin $doctrineAdmin
		 */
		public function __construct($name, $value, $type, $length, DoctrineAdmin $doctrineAdmin) {
			$this->name = $name;
			$this->value = $value;
			$this->type = $type;
			$this->length = $length;
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
		
		public function __toString() {
			$value = $this->getValue();
			if ($value instanceof DateTime) {
				return $value->format('Y-m-d');
			} elseif ($value === null) {
				return 'NULL';
			} else {
				return (string) $value;
			}
		}
		
		public function setValueFromString($string) {
			switch($this->getType()) {
				case 'date':
				case 'datetime':
					$this->setValue(new DateTime($string));
					break;
				default:
					$this->setValue($string);
			}
		}
	}
}